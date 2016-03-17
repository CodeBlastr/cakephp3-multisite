# Multisite Support for CakePHP 3.x
A plugin that allows you to have a single Cakephp 3.x install support multiple websites. 

## Install

In console run
```php
composer require codeblastr/multisite
```
Replace the first 6 lines  of APP/config/bootstrap.php with the following. (It's **important** to load the plugin in this place because we need access to the constant SITE_DIR defined early on. 
```php
<?php
/**
 * Configure paths required to find CakePHP + general filepath
 * constants
 */
require __DIR__ . '/paths.php';

/**
 * Get multisites
 */
Plugin::load('CodeBlastr/MultiSite', ['bootstrap' => true]);
```

In APP/composer.json add "App\\Console\\AutoLoader::postAutoloadDump" to "post-autoload-dump" like this
```php
"scripts": {
    "post-install-cmd": "App\\Console\\Installer::postInstall",
    "post-autoload-dump": [
        "Cake\\Composer\\Installer\\PluginInstaller::postAutoloadDump",
        "CodeBlastr\\MultiSite\\Console\\AutoLoader::postAutoloadDump"
    ]
},
```

## Usage

If there is a plugin which you want to allow sites to override, you need to add it to the autoload parameter of your main app composer.json file.  Formatted as ``"VendorName\\PluginName\\": "./SITE_DIR/vendor/[vendor name]/[plugin name]/src"``, for example...

```php
// APP/composer.json

"autoload": {
    "psr-4": {
        "App\\": "src",
        "CodeBlastr\\Multisite\\": "./SITE_DIR/vendor/codeblastr/multisite/src"
    }
},
```
