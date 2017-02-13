<?php
/**
 * Created by PhpStorm.
 * User: sanderhagenaars
 * Date: 08/02/2017
 * Time: 20.45
 */

namespace NobrainerWeb\Blocks;


class BlockExtension extends \DataExtension
{
    public function updateCMSFields(\FieldList $fields)
    {
        $fields->push(\HiddenField::create('PageID'));
        $fields->push(\HiddenField::create('BlockSort'));
        $fields->push(\HiddenField::create('BlockType'));

        if ($this->getAction() == 'new') {
            //return $this->getBlockSelectionFields($fields);
        }
    }

    /**
     * Get the new or edit action
     *
     * @since version 1.0.0
     *
     * @return string
     **/
    private function getAction()
    {
        $path = explode('/', \Controller::curr()->getRequest()->getURL());

        return array_pop($path);
    }


}