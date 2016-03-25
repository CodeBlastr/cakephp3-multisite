<?php
namespace CodeBlastr\MultiSite\View;

use Cake\Core\App;
use Cake\Utility\Inflector;
use Cake\View\View;

/**
 * MultiSite View
 *
 * Customized to handle paths within a sites folder.
 * The goal being to match the directory structure of
 * an app with composer installed plugins.
 *
 * For example if a plugin exists at vendor/VendorName/PluginName
 *
 * Using the following you can override single controllers,
 * model or template files at a path like this, while all other
 * files will continue to fall back on the original plugin.
 *
 * ex.1  APP/sites/example.com/vendor/VendorName/PluginName/src/Controller/MyController.php
 * ex.2  APP/sites/example.com/vendor/VendorName/PluginName/src/Template/Prefix/PluginName/index.ctp
 * ex.3  APP/sites/example.com/vendor/VendorName/PluginName/src/Template/Layout/default.ctp
 *
 * In APP/config/app.php set App.paths.templates to this :
 * ``ROOT . DS . SITE_DIR . DS . 'vendor' . DS . '%s' . DS . 'src' . DS . 'Template'``
 *
 * In App/config/bootstrap.php add this after the first require :
 * ``require ROOT . DS . 'sites' . DS . 'bootstrap.php';``
 *
 * In App/sites/bootstrap.php you can something like this
 * define('SITE_DIR', 'sites/' . $_SERVER['HTTP_HOST']);
 *
 */
class MultiSiteView extends View
{
    /**
     * Paths for multi-site templates support.
     *
     * @param null $plugin
     * @param bool $cached
     * @return array
     */
    public function _paths($plugin = null, $cached = true)
    {
        if ($cached === true) {
            if ($plugin === null && !empty($this->_paths)) {
                return $this->_paths;
            }
            if ($plugin !== null && isset($this->_pathsForPlugin[$plugin])) {
                return $this->_pathsForPlugin[$plugin];
            }
        }
        $templatePaths = App::path('Template');
        $pluginPaths = $themePaths = [];
        if (!empty($plugin)) {
            for ($i = 0, $count = count($templatePaths); $i < $count; $i++) {
                // start customization
                // original // $pluginPaths[] = $templatePaths[$i] . 'Plugin' . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR;
                $pluginPaths[] = sprintf($templatePaths[$i] . DIRECTORY_SEPARATOR, $plugin);
                // end customization
            }
            $pluginPaths = array_merge($pluginPaths, App::path('Template', $plugin));
        }

        if (!empty($this->theme)) {
            $themePaths = App::path('Template', Inflector::camelize($this->theme));
            if ($plugin) {
                for ($i = 0, $count = count($themePaths); $i < $count; $i++) {
                    // start customization
                    // original // array_unshift($themePaths, $themePaths[$i] . 'Plugin' . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR);
                    array_unshift($themePaths, str_replace('vendor', SITE_DIR . DS . 'vendor', $themePaths[$i]));
                    // end customization
                }
            }
        }

        $paths = array_merge(
            $themePaths,
            $pluginPaths,
            $templatePaths,
            [dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Template' . DIRECTORY_SEPARATOR]
        );
        if ($plugin !== null) {
            return $this->_pathsForPlugin[$plugin] = $paths;
        }
        return $this->_paths = $paths;
    }
}