# 설치

- [요구사항](#requirements)
- [Composer 설치](#composer)
- [서비스 프로바이더](#service-provider)
- [설정과 에셋 배포](#publish)
- [관리자 설정 디렉터리](#administrator-directory)
- [최소 모델 설정](#minimal-model-config)
- [배포 시 에셋 갱신](#assets)

<a name="requirements"></a>
## 요구사항

Laravel Administrator 10.x는 현재 패키지 기준으로 다음 환경을 요구합니다.

| 항목 | 요구 버전 |
| --- | --- |
| PHP | `>= 8.1` |
| Laravel Framework | `10.*` |
| Composer | Laravel 애플리케이션 의존성 설치용 |

패키지 개발이나 문서 빌드를 직접 수행하는 경우에만 Node.js와 npm이 필요합니다. 일반 Laravel 애플리케이션에서 패키지를 사용하는 경우에는 Composer와 `php artisan vendor:publish`가 핵심 절차입니다.

<a name="composer"></a>
## Composer 설치

Laravel 10 애플리케이션 루트에서 패키지를 설치합니다.

```bash
composer require "saaksin/laravel-administrator:^10.6"
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

다음 단계는 [설정 문서](/docs/ko/configuration)와 [모델 설정 문서](/docs/ko/model-configuration)를 참고하십시오.
