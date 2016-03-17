# Multisite Support for CakePHP 3.x
A plugin that allows you to have a single Cakephp 3.x install support multiple websites. 

## Installation

In console run
```php
composer require codeblastr/multisite
```

Replace the first 6 lines  of APP/config/bootstrap.php with the following.
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
require ROOT . DS . 'sites' . DS . 'bootstrap.php';
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

In APP/config/app.php set App.paths.templates to this :
```php
'App' => [
	'paths' => [
		'templates' => [
			ROOT . DS . SITE_DIR . DS . 'vendor' . DS . '%s' . DS . 'src' . DS . 'Template',
			APP . 'Template' . DS
		],
	],
],
```

 - Create a folder at APP/sites/example.com and APP/sites/example-app-folder.
 - Within those folders you can create custom versions of core app files.
    - ex. APP/sites/example.com/config/app.php (create your own db connection here)
    - ex. APP/sites/example-app-folder/config/app.php (create a different db connection for this site here)


## Usage

Create a folder and file at APP/sites/bootstrap.php <sup><sub>(these are examples, change the names to domains that you actually want to use)</sub></sup>
```php
<?php
/**
 * Map domains this install will support, to where that site's files are located.
 * The incoming request domain is on the left, the folder within the sites
 * directory that the request maps to is on the right.
 */

$domains['example.com'] = 'example.com';
$domains['example-app.com'] = 'example-app-folder';

/**
 * find the folder that is being requested by the domain
 */
if (!empty($domains[$_SERVER['HTTP_HOST']])) {
    if (!defined('SITE_DIR')) {
        // this is the site combined local and remote sites directory
        define('SITE_DIR', 'sites/' . $domains[$_SERVER['HTTP_HOST']]);
    }
}
```


If there is a plugin which you want to allow individual sites to override, you need to add it to the
autoload parameter of your main app composer.json file.
Formatted as ``"VendorName\\PluginName\\": "./SITE_DIR/vendor/[vendor name]/[plugin name]/src"``, for example...


```php
// APP/composer.json

"autoload": {
    "psr-4": {
        "App\\": "src",
        "CodeBlastr\\Multisite\\": "./SITE_DIR/vendor/codeblastr/multisite/src"
    }
},
```
