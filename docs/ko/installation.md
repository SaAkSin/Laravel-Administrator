# 설치

- [요구사항](#requirements)
- [Composer 설치](#composer)
- [서비스 프로바이더](#service-provider)
- [설정과 에셋 배포](#publish)
- [관리자 설정 디렉터리](#administrator-directory)
- [최소 모델 설정](#minimal-model-config)
- [Laravel Octane](#octane)
- [배포 시 에셋 갱신](#assets)

<a name="requirements"></a>
## 요구사항

Laravel Administrator 13.0은 다음 환경을 요구합니다.

| 항목 | 요구 버전 |
| --- | --- |
| PHP | `^8.3` |
| Laravel Framework | `^13.0` |
| Composer | Laravel 애플리케이션 의존성 설치용 |

패키지 개발이나 문서 빌드를 직접 수행하는 경우에만 Node.js와 npm이 필요합니다. 일반 Laravel 애플리케이션에서 패키지를 사용하는 경우에는 Composer와 `php artisan vendor:publish`가 핵심 절차입니다.

<a name="composer"></a>
## Composer 설치

Laravel 13 애플리케이션 루트에서 패키지를 설치합니다.

```bash
composer require "saaksin/laravel-administrator:^13.0"
```

<a name="service-provider"></a>
## 서비스 프로바이더

Laravel의 패키지 자동 검색이 활성화되어 있으면 서비스 프로바이더는 자동 등록됩니다. 자동 검색을 끄고 운영하는 프로젝트라면 `config/app.php`에 직접 등록합니다.

```php {4}
'providers' => [
    // ...

    SaAkSin\Administrator\AdministratorServiceProvider::class,
],
```

<a name="publish"></a>
## 설정과 에셋 배포

설정 파일과 공개 에셋을 한 번에 배포하려면 서비스 프로바이더 기준으로 publish를 실행합니다.

```bash
php artisan vendor:publish --provider="SaAkSin\Administrator\AdministratorServiceProvider" --force
```

실행 후 다음 파일과 디렉터리가 호스트 프로젝트에 생성됩니다.

```text
config/administrator.php
public/packages/saaksin/administrator/
```

패키지 코드 기준으로 `--tag=laravel-administrator`는 공개 에셋 갱신용 태그입니다. 설정 파일까지 처음 배포해야 하는 설치 단계에서는 위의 `--provider` 명령을 사용하는 편이 안전합니다.

<a name="administrator-directory"></a>
## 관리자 설정 디렉터리

기본 설정 파일은 모델 설정을 프로젝트 루트의 `administrator/`에서 읽고, 설정 페이지 파일을 `administrator/settings/`에서 읽습니다.

```php {2-3}
return array(
    'model_config_path' => base_path('administrator'),
    'settings_config_path' => base_path('administrator/settings'),
);
```

처음 설치한 프로젝트에서는 디렉터리를 직접 생성합니다.

```bash
mkdir -p administrator/settings
```

그리고 `config/administrator.php`에서 최소한 `menu`, `home_page`, `permission`을 프로젝트 인증 구조에 맞게 조정합니다.

```php {4-6,8}
return array(
    'uri' => 'admin',
    'title' => 'Admin',
    'menu' => array(
        'users',
        'Settings' => array('settings.site'),
    ),
    'home_page' => 'users',
    'permission' => 'App\Http\Middleware\AdminPermission@check',
);
```

`permission`에는 Laravel 컨테이너가 호출할 수 있는 문자열, 클로저, 불리언 값을 사용할 수 있습니다. 기본값은 `App\Http\Middleware\AdminPermission@check`이므로 실제 프로젝트에 해당 클래스가 없다면 반드시 바꾸어야 합니다.

<a name="minimal-model-config"></a>
## 최소 모델 설정

`config/administrator.php`의 `menu`에 `users`를 추가했다면 `administrator/users.php` 파일을 만듭니다.

```php {5,7-16,18-31}
<?php

return array(
    'title' => '사용자',
    'single' => '사용자',
    'model' => App\Models\User::class,
    'columns' => array(
        'id' => array(
            'title' => 'ID',
        ),
        'name' => array(
            'title' => '이름',
        ),
        'email' => array(
            'title' => '이메일',
        ),
    ),
    'edit_fields' => array(
        'name' => array(
            'title' => '이름',
            'type' => 'text',
        ),
        'email' => array(
            'title' => '이메일',
            'type' => 'text',
        ),
        'password' => array(
            'title' => '비밀번호',
            'type' => 'password',
        ),
    ),
);
```

비밀번호 필드는 저장 전에 값을 숨기는 setter 필드로 동작합니다. 실제 해시는 Eloquent mutator 또는 모델 이벤트에서 처리하십시오.

```php {7}
use Illuminate\Database\Eloquent\Casts\Attribute;

protected function password(): Attribute
{
    return Attribute::make(
        set: fn ($value) => filled($value) ? bcrypt($value) : $this->password,
    );
}
```

<a name="octane"></a>
## Laravel Octane

Laravel 13 호스트 애플리케이션이 RoadRunner, Swoole 또는 FrankenPHP 기반 Laravel Octane에서 실행될 때 Administrator는 요청별 설정, 필드, 컬럼, 액션, 권한, 페이지당 행 수와 로케일 상태를 분리합니다. Octane은 호스트가 선택하는 실행 환경이며, 이 패키지는 운영 의존성으로 `laravel/octane`을 추가하거나 서버 드라이버 설치를 강제하지 않습니다.

Octane을 사용할 때는 호스트 애플리케이션에 Laravel 13 호환 Octane 2.x를 별도로 설치하고 구성하십시오. `administrator.ready` 이벤트는 애플리케이션 또는 워커가 부트될 때 한 번만 발생하므로 요청별 상태 초기화에 사용하면 안 됩니다.

실행 중인 Octane 환경에 패키지 코드나 설정 변경을 배포한 뒤에는 새 코드로 워커를 다시 부트합니다.

```bash
php artisan octane:reload
```

<a name="assets"></a>
## 배포 시 에셋 갱신

패키지를 업데이트한 뒤 공개 에셋만 최신 빌드로 다시 동기화하려면 에셋 태그를 사용합니다.

```bash
php artisan vendor:publish --tag=laravel-administrator --force
```

운영 배포에서 Composer 설치 또는 업데이트 뒤 자동으로 실행하려면 호스트 프로젝트의 `composer.json`에 스크립트를 추가할 수 있습니다.

```json {4,7}
{
  "scripts": {
    "post-install-cmd": [
      "php artisan vendor:publish --tag=laravel-administrator --force"
    ],
    "post-update-cmd": [
      "php artisan vendor:publish --tag=laravel-administrator --force"
    ]
  }
}
```

호스트 애플리케이션이 Octane을 사용한다면 위 갱신 절차 뒤에 `php artisan octane:reload`도 실행하십시오.

다음 단계는 [설정 문서](./configuration.md)와 [모델 설정 문서](./model-configuration.md)를 참고하십시오.
