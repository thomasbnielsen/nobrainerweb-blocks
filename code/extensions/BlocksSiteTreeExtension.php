<?php
/**
 * Created by PhpStorm.
 * User: sanderhagenaars
 * Date: 08/02/2017
 * Time: 19.40
 */

namespace NobrainerWeb\Blocks;


class BlocksSiteTreeExtension extends \DataExtension
{
    public function updateCMSFields(\FieldList $fields)
    {
        $gridfield = $fields->dataFieldByName('Blocks');

        if(!$gridfield){
            return;
        }

        $gridfield->getConfig()
            ->removeComponentsByType('GridFieldAddNewMultiClass')
            ->addComponent(new GridFieldAddNewBlock());

    }

}