# Multisite Support for CakePHP 3.x
A plugin that allows you to have a single Cakephp 3.x app which supports multiple websites, while being
flexible enough that you can customize individual files on a per site basis.

For example: if a request is made to example.com, you might have some files like
``APP/sites/example.com/config/app.php``, and ``APP/sites/example.com/vendor/CakeDC/Users/src/Controllers/UsersController.php``,
both of which would override the respective default file in the core APP on a per site basis.

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

In APP/composer.json add ```"CodeBlastrMultiSite\\Console\\AutoLoader::postAutoloadDump"`` to ``"post-autoload-dump"`` like this:
```php
"scripts": {
    "post-install-cmd": "App\\Console\\Installer::postInstall",
    "post-autoload-dump": [
        "Cake\\Composer\\Installer\\PluginInstaller::postAutoloadDump",
        "CodeBlastrMultiSite\\Console\\AutoLoader::postAutoloadDump"
    ]
},
```

In APP/config/app.php set App.paths.templates like this:
```php
'App' => [
	'paths' => [
		'templates' => [
			ROOT . DS . SITE_DIR . DS . 'vendor' . DS . '%s' . DS . 'src' . DS . 'Template',
			ROOT . DS . SITE_DIR . DS . 'src' . DS . 'Template' . DS,
			APP . 'Template' . DS,
			CORE_PATH . 'src'. DS . 'Template' . DS
		],
	],
],
```

In APP/src/View/AppView.php add both of the following:
```php
// near the top of the file, outside of class AppView()

use CodeBlastrMultiSite\View\MultisiteView;

// inside of class AppView()
/**
 * Overwrites Cake/View::_paths()
 *
 * @param null $plugin
 * @param bool $cached
 * @return mixed
 */
protected function _paths($plugin = null, $cached = true)
{
	$multisite = new MultisiteView();
	$multisite->theme = $this->theme;
	return $multisite->_paths($plugin, $cached);
}
```


## Usage

 - Create a folder at APP/sites/example.com and APP/sites/example-app-folder.
 - Within those folders you can create custom versions of core app files.
    - ex. APP/sites/example.com/config/app.php (create your own db connection here)
    - ex. APP/sites/example-app-folder/config/app.php (create a different db connection for this site here)

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


If there is a plugin  that you want individual sites to have access to customize or override you need to add it to the
autoload parameter of your main ``APP/composer.json`` file.
Formatted as ``"VendorName\\PluginName\\": "./SITE_DIR/vendor/[vendor name]/[plugin name]/src"``, for example...


```php
// APP/composer.json

"autoload": {
    "psr-4": {
        "App\\": "src",
        "CodeBlastrMultiSite\\": "./SITE_DIR/vendor/codeblastr/multisite/src"
    }
},
```

Anytime you add a new plugin to this autoload you'll need to run this in console
```php
composer dump-autoload
```
