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
        // if any predefined block field values have been included in the url, use them to auto fill out some fields
        if($vals = $this->getPredefinedBlockValues()){

            foreach($vals as $name => $val){
                $field = $fields->dataFieldByName($name);
                if($field){
                    $field->setValue($val);
                }
            }

        }
    }

    /**
     * There might be some GET variables containing field values
     *
     * @return array|null
     */
    public function getPredefinedBlockValues()
    {
        $request = \Controller::curr()->getRequest();
        $params = $request->getVars();

        if(isset($params['url'])){
            unset($params['url']);
        }

        return !empty($params) ? $params : null;
    }

}