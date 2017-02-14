<?php

namespace NobrainerWeb\Blocks;

/**
 * Request handler for creating new blocks in the CMS
 *
 * @package silverstripe-block-page
 * @license MIT License https://github.com/cyber-duck/silverstripe-block-page/blob/master/LICENSE
 * @author  <andrewm@cyber-duck.co.uk>
 **/
class ChooseBlockModel_ItemRequest extends \GridFieldDetailForm_ItemRequest
{
    private static $allowed_actions = array(
        //'edit',
        //'view',
        'ItemEditForm'
    );

    private $title;


    public function Link($action = null) {
        if($this->record->ID) {
            return parent::Link($action);
        } else {
            return \Controller::join_links(
                $this->gridField->Link(), 'choose-block-model', get_class($this->record)
            );
        }
    }

    public function ItemEditForm()
    {
        $form = parent::ItemEditForm();
        $actions = \FieldList::create();

        // we do this to avoid Versioning altering these buttons.
        // On this item request we dont want anything to be saved so no versioning required
        // When versioned alted the buttons to save draft and publish, it kind of breaks things
        // We really just want to redirect users, not do anything fancy

        $actions->push(\FormAction::create('doSave', _t('GridFieldDetailForm.Create', 'Create'))
            ->setUseButtonTag(true)
            ->addExtraClass('ss-ui-action-constructive')
            ->setAttribute('data-icon', 'add'));

        // Add a Cancel link which is a button-like link and link back to one level up.
        $curmbs = $this->Breadcrumbs();
        if($curmbs && $curmbs->count()>=2){
            $one_level_up = $curmbs->offsetGet($curmbs->count()-2);
            $text = sprintf(
                "<a class=\"%s\" href=\"%s\">%s</a>",
                "crumb ss-ui-button ss-ui-action-destructive cms-panel-link ui-corner-all", // CSS classes
                $one_level_up->Link, // url
                _t('GridFieldDetailForm.CancelBtn', 'Cancel') // label
            );
            $actions->push(\LiteralField::create('cancelbutton', $text));
        }


        // overwrite this to get custom fields
        // otherwise it would show a standard blocks CMS fields
        $form->__set('actions', $actions);
        $form->__set('fields', $this->getBlockSelectionFields());

        $this->extend("updateChooseBlockModelForm", $form);

        return $form;
    }

    public function doSave($data, $form)
    {
        if(isset($data['BlockStage']) && $data['BlockStage'] == 'choose'){
            $controller = $this->getToplevelController();
            $link = \Controller::join_links(
                $this->gridField->Link(), 'new-block', $data['BlockType']
            ); // link on to edit the block in the normal way of editing dataobjects

            // include GET vars to indicate preset fields on the newly created block
            // this could be the chosen template
            $extravars = '';
            foreach($data as $key => $val){
                // check if its meant to be used as predefined field
                $pieces = explode('_', $key);
                if(isset($pieces[1]) && $pieces[0] == 'BlockSetting'){
                    if($extravars){
                        $extravars .= '&' . $pieces[1] = '=' . $val;
                    } else {
                        $extravars .= '?' . $pieces[1] . '=' . $val;
                    }
                }
            }

            return $controller->redirect($link . $extravars);
        } else {
            return parent::doSave($data, $form);
        }
    }

    /**
     * Gets the button title text.
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the button title text.
     *
     * @param string $title
     * @return ChooseBlockModel_ItemRequest $this
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * Create the CMS block selector fields
     *
     * @since version 1.0.0
     *
     * @return object
     **/
    public function getBlockSelectionFields()
    {
        $fields = \FieldList::create();

        $fields->push(\LiteralField::create(false, '<div id="BlockType">'));
        $fields->push(\OptionsetField::create('BlockType', $this->getBlockSelectionLabel(), $this->getBlockSelectionOptions())
            ->setCustomValidationMessage('Please select a block type'));
        $fields->push(\LiteralField::create(false, '</div">'));

        // this field is quite important see doSave()
        $fields->push(\HiddenField::create('BlockStage')->setValue('choose'));

        // use field with names "BlockSetting_{field_name}" to set predefined fields for that block
        //$fields->push(\OptionsetField::create('BlockSetting_Template', $this->getBlockSelectionLabel(), $this->getBlockSelectionOptions()));

        return $fields;
    }

    /**
     * Create the CMS block selector field label
     *
     * @since version 1.0.0
     *
     * @return string
     **/
    private function getBlockSelectionLabel()
    {
        $html = '<span class="step-label"><span class="flyout">%d</span><span class="arrow"></span><span class="title">%s</span></span>';

        return sprintf($html, 1, 'Add content block');
    }

    /**
     * Return an array of block type dropdown options HTML
     *
     * @since version 1.0.0
     *
     * @return array
     **/
    private function getBlockSelectionOptions()
    {
        $types = array_values(\ClassInfo::subclassesFor('Block'));

        $html = '<span class="page-icon class-%s"></span>
                 <strong class="title">%s</strong>
                 <span class="description">%s</span>';

        $options = [];

        foreach ($types as $type) {
            $option = sprintf($html,
                $type,
                $type,
                \Config::inst()->get($type, 'description')
            );
            $options[$type] = \DBField::create_field('HTMLText', $option);
        }
        return $options;
    }

    /**
     * Populates an array of classes in the CMS
     * which allows the user to change the page type.
     *
     * @return SS_List
     */
    /*
    public function BlockTypes() {
        $classes = ClassInfo::subclassesFor('Block');

        $result = new ArrayList();

        foreach($classes as $class) {
            $instance = singleton($class);

            if($instance instanceof HiddenClass) continue;

            // skip this type if it is restricted
            if($instance->stat('need_permission') && !$this->can(singleton($class)->stat('need_permission'))) continue;

            $addAction = $instance->i18n_singular_name();

            // Get description (convert 'Page' to 'SiteTree' for correct localization lookups)
            $description = _t((($class == 'Page') ? 'SiteTree' : $class) . '.DESCRIPTION');

            if(!$description) {
                $description = $instance->uninherited('description');
            }

            if($class == 'Page' && !$description) {
                $description = singleton('SiteTree')->uninherited('description');
            }

            $result->push(new ArrayData(array(
                'ClassName' => $class,
                'AddAction' => $addAction,
                'Description' => $description,
                // TODO Sprite support
                'IconURL' => $instance->stat('icon'),
                'Title' => singleton($class)->i18n_singular_name(),
            )));
        }

        $result = $result->sort('AddAction');

        return $result;
    }
    */
}
