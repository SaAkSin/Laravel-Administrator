# 설정

- [소개](#introduction)
- [설정 파일 배포](#publish)
- [전체 예시](#example)
- [기본 라우트 옵션](#route-options)
- [설정 파일 경로](#config-paths)
- [메뉴](#menu)
- [권한](#permission)
- [대시보드와 홈 페이지](#dashboard)
- [이동 경로](#paths)
- [목록과 로케일](#list-and-locales)

<a name="introduction"></a>
## 소개

Administrator의 전역 동작은 Laravel 애플리케이션의 `config/administrator.php`에서 제어합니다. 이 파일은 패키지의 기본 설정(`src/config/administrator.php`)을 publish한 뒤 프로젝트에 맞게 수정합니다.

모델별 화면은 `administrator/*.php`, 설정 페이지는 `administrator/settings/*.php`에 별도 파일로 둡니다. 전역 설정의 `menu` 항목은 이 파일 이름과 연결됩니다.

<a name="publish"></a>
## 설정 파일 배포

처음 설치할 때는 서비스 프로바이더 기준 publish를 실행해 설정 파일과 공개 에셋을 함께 배포합니다.

```bash
php artisan vendor:publish --provider="SaAkSin\Administrator\AdministratorServiceProvider" --force
```

패키지 업데이트 후 공개 에셋만 갱신할 때는 `laravel-administrator` 태그를 사용합니다.

```bash
php artisan vendor:publish --tag=laravel-administrator --force
```

<a name="example"></a>
## 전체 예시

아래 예시는 Laravel 10 프로젝트에서 바로 사용할 수 있는 최소 전역 설정입니다.

```php {6-9,11,13}
<?php

return array(
    'uri' => 'admin',
    'title' => 'Admin',
    'model_config_path' => base_path('administrator'),
    'settings_config_path' => base_path('administrator/settings'),
    'middleware' => array('web', 'auth'),
    'permission' => 'App\Http\Middleware\AdminPermission@check',
    'menu' => array(
        'users',
        'Settings' => array('settings.site'),
    ),
    'use_dashboard' => false,
    'home_page' => 'users',
    'login_path' => 'login',
    'login_redirect_key' => 'redirect',
    'logout_path' => false,
    'global_rows_per_page' => 20,
    'locales' => array('ko', 'en'),
);
```

`menu`는 비어 있으면 안 됩니다. `model_config_path`와 `settings_config_path`도 실제 디렉터리여야 하므로 설치 직후에는 다음 디렉터리를 생성하십시오.

```bash
mkdir -p administrator/settings
```

<a name="route-options"></a>
## 기본 라우트 옵션

### Uri

```php
'uri' => 'admin',
```

관리자 화면의 기본 URL 접두사입니다. 위 설정에서는 `/admin` 아래에 Administrator 라우트가 등록됩니다.

### Domain

```php
'domain' => '',
```

관리자 라우트를 특정 도메인이나 서브도메인에만 바인딩해야 할 때 사용합니다. 제한하지 않으려면 빈 문자열을 유지합니다.

### Middleware

```php {1}
'middleware' => array('web', 'auth'),
```

관리자 라우트에 추가할 Laravel 미들웨어 목록입니다. 패키지 내부에서는 `web`과 `ValidateAdmin` 미들웨어를 기본으로 병합하며, 위 설정처럼 `auth`를 추가해 로그인 사용자만 접근하도록 구성할 수 있습니다.

<a name="config-paths"></a>
## 설정 파일 경로

### Model Config Path

```php {1}
'model_config_path' => base_path('administrator'),
```

모델 설정 파일을 읽을 디렉터리입니다. 예를 들어 `menu`에 `users`가 있으면 `administrator/users.php` 파일을 찾습니다.

### Settings Config Path

```php {1}
'settings_config_path' => base_path('administrator/settings'),
```

설정 페이지 파일을 읽을 디렉터리입니다. 예를 들어 `menu`에 `settings.site`가 있으면 `administrator/settings/site.php` 파일을 찾습니다.

<a name="menu"></a>
## 메뉴

`menu`는 관리자 좌측 메뉴와 라우팅 가능한 리소스를 동시에 정의합니다.

```php {3-5,7,8}
'menu' => array(
    '콘텐츠' => array(
        'posts',
        'categories',
        'comments',
    ),
    'Settings' => array('settings.site'),
    'Analytics' => array('page.admin.analytics'),
),
```

메뉴 값은 다음 규칙을 따릅니다.

| 값 | 의미 | 필요한 파일 |
| --- | --- | --- |
| `users` | 모델 관리 화면 | `administrator/users.php` |
| `settings.site` | 설정 페이지 | `administrator/settings/site.php` |
| `page.admin.analytics` | 커스텀 Blade 뷰 | `resources/views/admin/analytics.blade.php` |

중첩 배열을 사용하면 메뉴 그룹을 만들 수 있습니다. 배열의 문자열 값은 파일명과 대소문자까지 일치해야 합니다.

<a name="permission"></a>
## 권한

전역 `permission`은 Administrator 전체 접근 가능 여부를 결정합니다.

```php {1}
'permission' => 'App\Http\Middleware\AdminPermission@check',
```

문자열은 Laravel 컨테이너의 `app()->call()`로 실행됩니다. 클로저나 불리언 값도 사용할 수 있습니다.

```php
'permission' => function () {
    return auth()->check() && auth()->user()->can('admin.access');
},
```

false로 평가되면 `login_path`로 리다이렉트되고, 세션에는 `login_redirect_key`에 지정한 이름으로 이전 URL이 저장됩니다.

<a name="dashboard"></a>
## 대시보드와 홈 페이지

대시보드를 별도 Blade 뷰로 보여주려면 `use_dashboard`를 켭니다.

```php {1-2}
'use_dashboard' => true,
'dashboard_view' => 'admin.dashboard',
```

대시보드를 쓰지 않는 경우에는 메뉴 항목 중 하나를 홈으로 지정합니다.

```php {1-2}
'use_dashboard' => false,
'home_page' => 'users',
```

`home_page` 값은 `menu`에 등록된 모델, 설정 페이지, 커스텀 페이지 중 하나와 일치해야 합니다.

<a name="paths"></a>
## 이동 경로

```php
'back_to_site_path' => '/',
'login_path' => 'login',
'logout_path' => false,
'login_redirect_key' => 'redirect',
```

`back_to_site_path`는 관리자 화면에서 사이트로 돌아가기 링크에 사용됩니다. `logout_path`에 URL 또는 라우트 문자열을 지정하면 우측 상단에 로그아웃 링크가 표시됩니다.

<a name="list-and-locales"></a>
## 목록과 로케일

```php {1,2}
'global_rows_per_page' => 20,
'locales' => array('ko', 'en'),
```

`global_rows_per_page`는 모델 목록의 기본 페이지당 행 수입니다. `locales`에 둘 이상의 로케일을 넣으면 관리자 화면에서 언어 선택 UI를 사용할 수 있습니다.

다음 단계는 [모델 설정](/docs/ko/model-configuration), [세팅 설정](/docs/ko/settings-configuration), [필드](/docs/ko/fields)를 참고하십시오.
