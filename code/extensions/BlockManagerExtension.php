<?php
/**
 * Created by PhpStorm.
 * User: tn
 * Date: 08/02/2017
 * Time: 19.40
 */

namespace NobrainerWeb\Blocks;


class BlockManagerExtension extends \DataExtension
{
    /**
     * Gets an array of all models defined for the current theme.
     *
     * @param string $theme
     * @param bool $keyAsValue
     *
     * @return array $model
     **/
    public function getTemplatesForTheme($class, $theme = null, $keyAsValue = false)
    {
        $theme = $theme ? $theme : $this->findTheme();
        if (!$theme) {
            return false;
        }
        $config = $this->owner->config()->get('themes');
        if (!isset($config[$theme]['models'])) {
            return false;
        }
        $templates = $config[$theme]['models'][$class]['templates'];

        return array_combine($templates, $templates);
    }

    /*
    * Get the current/active theme or 'default' to support theme-less sites
    */
    public function findTheme()
    {
        $currentTheme = \Config::inst()->get('SSViewer', 'theme');

        // check directly on SiteConfig incase ContentController hasn't set
        // the theme yet in ContentController->init()
        if (!$currentTheme && class_exists('SiteConfig')) {
            $currentTheme = \SiteConfig::current_site_config()->Theme;
        }

        return $currentTheme ? $currentTheme : 'default';
    }
}