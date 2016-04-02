<?php
namespace CodeBlastrMultiSite\Console;

use Composer\Script\Event;

/*
 * Updates the composer generated file autoload_psr4.php
 * to support multiple domains coming to a single install.
 *
 */
class AutoLoader
{

    /**
     * Called whenever composer (re)generates the autoloader
     *
     * @param Event $event the composer event object
     * @return void
     */
    public static function postAutoloadDump(Event $event)
    {
        $composer = $event->getComposer();
        $config = $composer->getConfig();

        $vendorDir = realpath($config->get('vendor-dir'));

        $configFile = static::_configFile($vendorDir);
        static::writeConfigFile($configFile);
    }

    /**
     * Path to the autoload_ps4 file
     *
     * @param string $vendorDir path to composer-vendor dir
     * @return string absolute file path
     */
    protected static function _configFile($vendorDir)
    {
        return $vendorDir . DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR . 'autoload_psr4.php';
    }

    /**
     * Rewrite the config file with the SITE_DIR constant included
     *
     * @param string $configFile the path to the config file
     * @return void
     */
    public static function writeConfigFile($configFile)
    {
        $contents = file_get_contents($configFile);
        $contents = str_replace('SITE_DIR', '\' . SITE_DIR . \'', $contents);
        file_put_contents($configFile, $contents);
    }
}
