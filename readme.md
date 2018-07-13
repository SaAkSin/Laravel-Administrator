# Laravel Administrator

기존 Frozonnode 의 Laravel-Administrator 가 더 이상 업데이트 되지않아, 이를 바탕으로하여 패키지를 업그레이드 및 개발하고 있습니다. Administrator is an administrative interface builder for [Laravel](http://laravel.com). With Administrator you can visually manage your Eloquent models and their relations, and also create stand-alone settings pages for storing site data and performing site tasks.

- **Author:** 이기석
- **Website:** [https://github.com/SaAkSin/Laravel-Administrator](https://github.com/SaAkSin/Laravel-Administrator)
- **Version:** 5.1.0

[![Build Status](https://travis-ci.org/FrozenNode/Laravel-Administrator.png?branch=master)](https://travis-ci.org/FrozenNode/Laravel-Administrator)

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/overview.jpg" />

## Composer

To install Administrator as a Composer package to be used with Laravel 5, simply run:

```sh
composer require "saaksin/administrator: 5.*"
```

Once it's installed, you can register the service provider in `config/app.php` in the `providers` array:

```php
'providers' => [
	'SaAkSin\Administrator\AdministratorServiceProvider',
]
```

Then publish Administrator's assets with `php artisan vendor:publish`. This will add the file `config/administrator.php`. This [config file](http://administrator.frozennode.com/docs/configuration) is the primary way you interact with Administrator. This command will also publish all of the assets, views, and translation files.

### Laravel 4

더 이상 지원하지 않습니다.

### Laravel 3

더 이상 지원하지 않습니다.

## Documentation

The complete docs for Administrator can be found at http://administrator.frozennode.com. You can also find the docs in the `/src/docs` directory.


## Copyright and License
Administrator was written by Jan Hartigan of Frozen Node for the Laravel framework.
Administrator is released under the MIT License. See the LICENSE file for details.


## Recent Changelog

### 5.1.0

### 5.0.12
- Bugfix: Added the table name to the constraint query
- Bugfix: Russian translate in knockout.php
- Bugfix: Update settings.php for `Request::url()` instead of using `url()`

### 5.0.11
- Bugfix: Had to make the middleware additon backwards compatable
- Docs: Added doc updates

### 5.0.10
- Bugfix: L5.2 changed the way url() works so added url('/') instead
- Added: Support for middleware additions to the Admin routes via the administrator config
- Testfix: Fixed failing tests

### 5.0.9
- Bugfix: Fix for datetimepicker partially being covered by top menu  

### 5.0.8
- Bugfix: Rename Bool class to Boolean to fix PHP 7 
- Bugfix: added missing custom attribute field

### 5.0.7
- Bugfix: Fixed boolean true bug 
- Bugfix: Fixes a bug where soft deletes are not being properly detected in L5

### 5.0.6
- Added: Support for custom domains in the admin routes
- Added: Ability to access the model from withinthe column output renderer
- Added: Dynamic Form Request Validation

### 5.0.5
- Added: Added password field to the settings view
- Added: Romanian Language
- Added: Basic HasMany Implementation along with re-ordering support
- Bugfix: Autocomplete working with default value
- Bugfix: Adding missing session to Admin Controller
- Bugfix: Fixed improper handling of filter value 0 for Enum/Text field
- Docfix: Simplified the composer command in the install docs to match the packagist.org instuctions
- Docfix: Changed the type definition for global_rows_per_page to int instead of Null|nt since Null causes divide by 0 error

### 5.0.4
- Testfix: fixing tests and js for gulp

### 5.0.3
- Cherry Picking v4.16.7

### 5.0.2
- Cherry Picking v4.16.6

### 5.0.1
- Bugfix: Fixing csrf token mismatches for some requests

### 5.0.0
- Upgraded to Laravel 5
- New translations (az)
