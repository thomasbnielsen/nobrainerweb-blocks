<?php

/**
 * Class to add some more methods to BlockManager, to suit our needs
 */

namespace NobrainerWeb\Blocks;


class BlockManager extends \BlockManager
{

    /**
     * $block_type is the name of the block
     *
     * $setting could be 'view_settings.outer-spacing.top'.
     *
     * This method should also check if val exists or something, so we dont get any hard crashes
     *
     * If you use this method, you should know the expected returned value as it can differ
     *
     * @param string $block_type
     * @param string  $setting
     * @return mixed (could be array, string, bool)
     */
    public static function getBlockConfigSetting($block_type, $setting)
    {
        return Helper::getConfSetting($block_type, $setting);
    }

}