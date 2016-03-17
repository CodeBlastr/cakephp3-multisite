# cakephp3-multisite
A plugin that allows you to have a single Cakephp 3.x install support multiple websites. 

## Install

```php
// In console run
composer require codeblastr/cakephp3-multisite
```

```php
// In config/bootstrap.php
Plugin::load('CodeBlastr/Multisite');
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
