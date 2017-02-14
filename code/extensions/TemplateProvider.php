<?php
/**
 * Created by PhpStorm.
 * User: tn
 * Date: 08/02/2017
 * Time: 19.40
 */

namespace NobrainerWeb\Blocks;

class TemplateProvider extends \DataExtension
{

    private static $db = array(
        'Template' => "Varchar"
    );

    public function updateCMSFields(\FieldList $fields)
    {
        $manager = singleton('BlockManager');
        $templates = $manager->getTemplatesForTheme($this->owner->ClassName);


        $fields->addFieldToTab('Root.Template',
            \DropdownField::create('Template', 'Choose a template', $templates)->setEmptyString('Choose'));
    }

    /**
     * @return string
     */
/*    public function TemplatedBlock()
    {
        if (\SSViewer::hasTemplate($this->owner->Template)) {
            return $this->owner->renderWith(array($this->owner->Template, 'Block'));
        }
    }
*/
}