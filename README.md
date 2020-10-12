[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Distilleries/Security/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Distilleries/Security/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Distilleries/Security/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Distilleries/Security/?branch=master)
[![Build Status](https://travis-ci.org/Distilleries/Security.svg?branch=master)](https://travis-ci.org/Distilleries/Security)
[![Total Downloads](https://poser.pugx.org/distilleries/Security/downloads)](https://packagist.org/packages/distilleries/Security)
[![Latest Stable Version](https://poser.pugx.org/distilleries/Security/version)](https://packagist.org/packages/distilleries/Security)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE.md) 

# Security
Is package to sanitize each data from middleware or it's can me use in standalone to sinitize strings.

## Table of contents

1. [Require](#require)
1. [Installation](#installation)


## Require

1. Php 7.1.3 or more

## Installation

Add on your composer.json

``` json
    "require": {
        "distilleries/security": "1.*",
    }
```

run `composer update`.


Publish the configuration:

```ssh
php artisan vendor:publish --provider="Distilleries\Security\SecurityServiceProvider"
```

## Configurations

```php
    return [
       'xss_enable'=> env('SECURITY_XSS_ENABLE',true),
       'html_purifier'=> env('SECURITY_HTML_PURIFIER_ENABLE',true)
    ];
```

Field | Usage
----- | -----
xss_enable | Enable Xss Clean on Middleware
html_purifier | Enable Html purifier on Middleware


Add the Middleware on the kernel file. 

```php

    protected $middleware = [
        \Distilleries\Security\Http\Middleware\XSS::class
    ];
```


## Standalone usage
You can use the class Security to sanitize data directly 

### Sinitize string

```php
    $xss = new \Distilleries\Security\Helpers\Security();
    $xss->xss_clean('<a href="javascript:aler('test')">Click to alert</a>');
```
>Should return <a >Click to alert</a>

### Entity decode

This function is a replacement for html_entity_decode()

The reason we are not using `html_entity_decode() by itself is because while it is not technically correct to leave out the semicolon at the end of an entity most browsers will still interpret the entity correctly.  html_entity_decode() does not convert entities without semicolons, so we are left with our own little solution here. Bummer.


```php
    $xss = new \Distilleries\Security\Helpers\Security();
    $xss->entity_decode(&lt;a href=&quot;javascript:alert('test')&quot;&gt;Test&lt;/a&gt;');
```
>Should return <a href="javascript:alert('test')">Test</a>

### Sinitize file path 
```php
    $xss = new \Distilleries\Security\Helpers\Security();
    $xss->sanitize_filename('./../test.jgp',true);
```

>Should display ./test.jpg instead of ./../test.jgp. The last parameter it's to allow or disallow relative path


```php
    $xss = new \Distilleries\Security\Helpers\Security();
    $xss->sanitize_filename('./../test.jgp',false);
```

>Should display test.jpg instead of ./../test.jgp.