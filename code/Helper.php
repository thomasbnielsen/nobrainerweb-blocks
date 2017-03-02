<?php
/**
 * Created by PhpStorm.
 * User: sanderhagenaars
 * Date: 02/03/2017
 * Time: 19.05
 */

namespace NobrainerWeb\Blocks;


class Helper
{
    /**
     * $classname is the name of the class
     *
     * $setting could be 'setting.childsetting.childsettingagain'.
     *
     * This method should also check if val exists or something, so we dont get any hard crashes
     *
     * If you use this method, you should know the expected returned value as it can differ
     *
     * @param string $classname
     * @param string  $setting
     * @return mixed (could be array, string, bool)
     */
    public static function getConfSetting($classname, $setting)
    {
        $val = null;

        $keys = explode('.', $setting);
        $lastElement = end($keys);
        $first_element = $keys[0];
        $running_val = \Config::inst()->get($classname, $first_element);

        foreach($keys as $key => $val){
            if($val != $lastElement){
                $next = $key + 1;
                $running_val = $running_val[$keys[$next]];
            }
        }

        $val = $running_val;

        return $val;
    }
}