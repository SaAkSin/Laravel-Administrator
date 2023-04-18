# Laravel Administrator

기존 Frozonnode 의 Laravel-Administrator 가 더 이상 업데이트 되지않아, 이를 바탕으로하여 패키지를 개발 및 업그레이드하고 있습니다. Administrator is an administrative interface builder for [Laravel](http://laravel.com). With Administrator you can visually manage your Eloquent models and their relations, and also create stand-alone settings pages for storing site data and performing site tasks.

- **Author:** 이기석
- **Website:** [https://github.com/SaAkSin/Laravel-Administrator](https://github.com/SaAkSin/Laravel-Administrator)
- **Version:** 10.0.0

[![Build Status](https://travis-ci.org/FrozenNode/Laravel-Administrator.png?branch=master)](https://travis-ci.org/FrozenNode/Laravel-Administrator)

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/overview.jpg" />

## Composer

To install Administrator as a Composer package to be used with Laravel 5, simply run:

```sh
composer require "saaksin/laravel-administrator: v5.8.*"
```

Once it's installed, you can register the service provider in `config/app.php` in the `providers` array:

```php
'providers' => [
	...
	SaAkSin\Administrator\AdministratorServiceProvider::class
]
```

이 후, `php artisan vendor:publish` 을 실행합니다. `config/administrator.php` [설정파일](https://github.com/SaAkSin/Laravel-Administrator/blob/dev-10/docs/configuration.md)이 추가 되고, public 디렉토리에 관련 에셋, 뷰, 언어 파일 등이 복사됩니다. 설정 파일들은 config 디렉토리 하위가 아닌, 프로젝트 루트 디렉토리에서 administrator, administrator/settings 에 위치합니다.

### 설정파일
설정파일명과 동일한 함수명으로 시작합니다. 가령, 설정 파일이 users.php 이라면, 반드시 users 함수를 통하여 설정(배열)을 반환합니다.(세션 등과 연계하여 조건에 따른 배열 결과를 반환할 수 있음)

```php
function users()
{
    return array(
        'title' => '사용자 관리',
        'single' => '사용자',
        'model' => App\User::class,
        .....

    );
}
```

### HTTPS
app/Providers/AppServiceProvider 에서 라우트의 경로를 https 가 되도록 지정합니다.
```php
public function boot(UrlGenerator $url)
{
    $url->forceScheme('https');
}
```

asset url 에 https 주소를 사용하도록 .env 에 ASSET_URL 을 지정합니다.
```php
ASSET_URL=https://도메인주소
```

### FULL TEXT 검색
filter 에서 MySQL 의 full text 검색을 지원합니다.(대용량 검색)

```php
'filters' => array(
    'no' => array(
        'title' => 'Number',
        'type' => 'fulltext_mysql'
    ),
),
```

### TEXT 빠른 검색
filter 에서 시작 단어 검색 및 포커스 아웃 이벤트 시 검색을 시작합니다.

```php
'filters' => array(
    'no' => array(
        'title' => 'Name',
        'type' => 'text_quick'
    ),
),
```

### 페이지 리로드
액션을 성공적으로 실행한 후, 현재 페이지를 리로드하는 기능을 제공합니다.

```php
'action' => array(
    'reload' => true
),

```

### VIEW 모델 지원 (실험중)
모델 설정에서 view 모델 여부를 설정할 수 있습니다.(아직은 실험적인 기능이며, 조회시 약간의 성능 개선이 있습니다. MySQL InnoDB)

```php
'view' => true
```

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

### 10.0.0
- 라라벨 10.0.0 지원

### 5.8.0
- 라라벨 5.8.0 지원

### 5.1.0
- 모델 파일내 세션 사용가능

