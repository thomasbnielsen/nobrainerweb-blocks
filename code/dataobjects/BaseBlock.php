<?php

/**
 * Created by PhpStorm.
 * User: tn
 * Date: 08/02/17
 * Time: 22.58
 */
class BaseBlock extends Block
{
    /**
     * If the singular name is set in a private static $singular_name, it cannot be changed using the translation files
     * for some reason. Fix it by defining a method that handles the translation.
     * @return string
     */
    public function singular_name()
    {
        return "Base block";
    }

    /**
     * If the plural name is set in a private static $plural_name, it cannot be changed using the translation files
     * for some reason. Fix it by defining a method that handles the translation.
     * @return string
     */
    public function plural_name()
    {
        return "Base blocks";
    }

    private static $db = array(
        'Content' => 'HTMLText',
    );

    public function fieldLabels($includeRelations = true)
    {
        return array_merge(
            parent::fieldLabels($includeRelations),
            array(
                'Content' => _t('Block.Content', 'Content'),
            )
        );
    }

    /**
     * Renders this block with either a choosen template
     * or falls back to the default render method of the module
     *
     * @return string
     **/
    public function forTemplate()
    {
        if ($this->Template && SSViewer::hasTemplate($this->Template)) {
            return $this->renderWith($this->Template);
        }

        parent::forTemplate();
    }
}
