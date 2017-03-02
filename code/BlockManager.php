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

    /**
     * @param $block_type
     * @param $template
     * @return string
     */
    public function getBlockTemplateThumbnailName($block_type, $template)
    {
        return $block_type . '_' . $template;
    }

    /**
     * TODO set path and filetype as property/config property
     * TODO check in active theme for any overwrites of thumbnails
     * TODO check if file exists else return empty string
     *
     * @param        $block_type
     * @param        $template
     * @param string $filetype
     * @param string $path
     * @return string
     */
    public function getBlockTemplateThumbnail($block_type, $template, $filetype = 'png', $path = 'nobrainerweb-blocks/images/template-thumbnails/')
    {
        $name = $this->getBlockTemplateThumbnailName($block_type, $template);

        return $path . $name . '.' . $filetype;
    }
}