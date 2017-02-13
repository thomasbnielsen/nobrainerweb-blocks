<?php

namespace NobrainerWeb\Blocks;

class GridFieldAddNewBlock implements \GridField_HTMLProvider, \GridField_URLHandler
{

    private static $allowed_actions = array(
        'handleChooseBlock',
        'handleAddNew'
    );

    private $fragment;

    private $title = 'Add';

    private $classes;

    private $defaultClass;

    /**
     * @var string
     */
    protected $itemRequestClass_Choose = 'NobrainerWeb\Blocks\ChooseBlockModel_ItemRequest';
    protected $itemRequestClass_AddNew = 'GridFieldDetailForm_ItemRequest';

    /**
     * @param string $fragment the fragment to render the button in
     */
    public function __construct($fragment = 'before')
    {
        $this->setFragment($fragment);
        $this->setTitle('Add block');
    }

    /**
     * {@inheritDoc}
     */
    public function getURLHandlers($grid)
    {
        return array(
            'choose-block-model/$ClassName!' => 'handleChooseBlock',
            'new-block/$ClassName!'          => 'handleAddNew',
        );
    }

    /**
     * Gets the fragment name this button is rendered into.
     *
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Sets the fragment name this button is rendered into.
     *
     * @param string $fragment
     * @return GridFieldAddNewBlock $this
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;

        return $this;
    }

    /**
     * Gets the button title text.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the button title text.
     *
     * @param string $title
     * @return GridFieldAddNewBlock $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the classes that can be created using this button, defaulting to the model class and
     * its subclasses.
     *
     * @param GridField $grid
     * @return array a map of class name to title
     */
    public function getClasses(\GridField $grid)
    {
        $result = array();

        if (is_null($this->classes)) {
            $classes = array_values(\ClassInfo::subclassesFor($grid->getModelClass()));
            sort($classes);
        } else {
            $classes = $this->classes;
        }

        $kill_ancestors = array();
        foreach ($classes as $class => $title) {
            if (!is_string($class)) {
                $class = $title;
            }
            if (!class_exists($class)) {
                continue;
            }
            $is_abstract = (($reflection = new \ReflectionClass($class)) && $reflection->isAbstract());
            if (!$is_abstract && $class === $title) {
                $title = singleton($class)->i18n_singular_name();
            }

            if ($ancestor_to_hide = \Config::inst()->get($class, 'hide_ancestor', \Config::FIRST_SET)) {
                $kill_ancestors[$ancestor_to_hide] = true;
            }

            if ($is_abstract || !singleton($class)->canCreate()) {
                continue;
            }

            $result[$class] = $title;
        }

        if ($kill_ancestors) {
            foreach ($kill_ancestors as $class => $bool) {
                unset($result[$class]);
            }
        }

        return $result;
    }

    /**
     * Sets the classes that can be created using this button.
     *
     * @param array $classes a set of class names, optionally mapped to titles
     * @return GridFieldAddNewBlock $this
     */
    public function setClasses(array $classes, $default = null)
    {
        $this->classes = $classes;
        if ($default) {
            $this->defaultClass = $default;
        }

        return $this;
    }

    /**
     * Sets the default class that is selected automatically.
     *
     * @param string $default the class name to use as default
     * @return GridFieldAddNewBlock $this
     */
    public function setDefaultClass($default)
    {
        $this->defaultClass = $default;

        return $this;
    }

    /**
     * Handles choosing a class for creation
     *
     * @param GridField      $grid
     * @param SS_HTTPRequest $request
     * @return ChooseBlockModel_ItemRequest
     */
    public function handleChooseBlock($grid, $request)
    {
        $class = $request->param('ClassName');
        $classes = $this->getClasses($grid);
        $component = $grid->getConfig()->getComponentByType('GridFieldDetailForm');

        if (!$component) {
            throw new \Exception('Requires the detail form component.');
        }

        if (!$class || !array_key_exists($class, $classes)) {
            throw new \SS_HTTPResponse_Exception(400);
        }

        $handler = \Object::create($this->itemRequestClass_Choose,
            $grid, $component, new $class(), $grid->getForm()->getController(), 'choose-block-model'
        );
        $handler->setTemplate($component->getTemplate());

        return $handler;
    }

    /**
     * Handles adding a new instance of a selected class.
     *
     * @param GridField      $grid
     * @param SS_HTTPRequest $request
     * @return GridFieldDetailForm_ItemRequest
     */
    public function handleAddNew($grid, $request)
    {
        $class = $request->param('ClassName');
        $component = $grid->getConfig()->getComponentByType('GridFieldDetailForm');

        if (!$component) {
            throw new \Exception('Requires the detail form component.');
        }

        if (!$class) {
            throw new \SS_HTTPResponse_Exception(400);
        }

        $handler = \Object::create($this->itemRequestClass_AddNew,
            $grid, $component, new $class(), $grid->getForm()->getController(), 'new-block'
        );
        $handler->setTemplate($component->getTemplate());

        return $handler;
    }

    /**
     * {@inheritDoc}
     */
    public function getHTMLFragments($grid)
    {

        $data = \ArrayData::create(array(
            'ButtonName' => $this->getTitle(),
            'NewLink'    => \Controller::join_links($grid->Link(), 'choose-block-model', 'Block'),
        ));

        return array(
            $this->getFragment() => $data->renderWith('GridFieldAddNewbutton')
        );
    }
}
