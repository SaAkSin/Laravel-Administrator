# Laravel Administrator

기존 Frozonnode 의 Laravel-Administrator 가 더 이상 업데이트 되지않아, 이를 바탕으로하여 패키지를 개발 및 업그레이드하고 있습니다. Administrator is an administrative interface builder for [Laravel](http://laravel.com). With Administrator you can visually manage your Eloquent models and their relations, and also create stand-alone settings pages for storing site data and performing site tasks.

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
- 모델 파일내 세션 사용가능

