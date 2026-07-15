# 세팅 설정

- [소개](#introduction)
- [파일 위치](#location)
- [기본 예제](#example)
- [필수 옵션](#required-options)
- [유효성 검사](#validation)
- [저장 전 처리](#before-save)
- [권한](#permission)
- [커스텀 액션](#custom-actions)
- [저장 경로](#storage-path)

<a name="introduction"></a>
## 소개

세팅 설정 파일은 Eloquent 모델로 표현하기 어려운 운영 설정을 관리자 화면에서 관리할 때 사용합니다. 예를 들어 사이트 이름, 로고, 캐시 정책, 외부 API 키 같은 값을 별도 설정 페이지로 만들 수 있습니다.

세팅 페이지는 `edit_fields`로 입력 UI를 정의하고, 저장 시 JSON 파일에 값을 보관합니다. 필요하면 `before_save` 콜백에서 데이터를 검증하거나 데이터베이스, 캐시, 외부 저장소로 직접 동기화할 수 있습니다.

<a name="location"></a>
## 파일 위치

세팅 설정 파일은 `config/administrator.php`의 `settings_config_path`에 둡니다. 기본값은 프로젝트 루트의 `administrator/settings/`입니다.

```php {2,5}
return array(
    'settings_config_path' => base_path('administrator/settings'),
    'menu' => array(
        'Settings' => array(
            'settings.site',
        ),
    ),
);
```

위 설정에서는 `administrator/settings/site.php` 파일이 필요하며, `/admin/settings/site` 화면과 연결됩니다.

<a name="example"></a>
## 기본 예제

```php {5-16,18-21}
<?php

return array(
    'title' => '사이트 설정',
    'edit_fields' => array(
        'site_name' => array(
            'title' => '사이트 이름',
            'type' => 'text',
        ),
        'admin_email' => array(
            'title' => '관리자 이메일',
            'type' => 'text',
        ),
        'maintenance_mode' => array(
            'title' => '점검 모드',
            'type' => 'bool',
        ),
    ),
    'rules' => array(
        'site_name' => 'required|max:50',
        'admin_email' => 'required|email',
    ),
);
```

<a name="required-options"></a>
## 필수 옵션

### Title

```php
'title' => '사이트 설정',
```

메뉴와 페이지 제목으로 사용됩니다.

### Edit Fields

```php {2,6,10}
'edit_fields' => array(
    'site_name' => array(
        'title' => '사이트 이름',
        'type' => 'text',
    ),
    'logo' => array(
        'title' => '로고',
        'type' => 'image',
    ),
    'page_cache_lifetime' => array(
        'title' => '페이지 캐시 시간',
        'type' => 'number',
    ),
);
```

세팅 페이지에서는 `key`, 관계 필드(`belongs_to`, `belongs_to_many`, `has_one`, `has_many`)를 제외한 필드 타입을 사용할 수 있습니다. 필드 옵션은 [필드 문서](./fields.md)를 참고하십시오.

<a name="validation"></a>
## 유효성 검사

```php {2-3}
'rules' => array(
    'site_name' => 'required|max:50',
    'admin_email' => 'required|email',
),
'messages' => array(
    'site_name.required' => '사이트 이름을 입력하십시오.',
),
```

`rules`와 `messages`는 Laravel validation 형식을 따릅니다. 저장 전 데이터가 유효하지 않으면 저장하지 않고 오류를 반환합니다.

<a name="before-save"></a>
## 저장 전 처리

```php {1,6}
'before_save' => function (&$data) {
    if (! str_ends_with($data['admin_email'], '@example.com')) {
        return '관리자 이메일은 example.com 도메인이어야 합니다.';
    }

    $data['site_name'] = trim($data['site_name']);
},
```

`before_save`는 기본 유효성 검사를 통과한 뒤 JSON 저장 전에 실행됩니다. `$data`는 참조로 전달되므로 값을 정리하거나 추가 저장 작업을 수행할 수 있습니다. 문자열을 반환하면 오류 메시지로 처리됩니다.

<a name="permission"></a>
## 권한

```php
'permission' => function () {
    return auth()->user()->can('settings.manage');
},
```

해당 설정 페이지를 볼 수 있는지 결정합니다. 문자열 콜백도 사용할 수 있습니다.

```php
'permission' => 'App\Admin\Permissions\SiteSettingPermission@access',
```

<a name="custom-actions"></a>
## 커스텀 액션

```php {2,8}
'actions' => array(
    'clear_page_cache' => array(
        'title' => '페이지 캐시 삭제',
        'messages' => array(
            'active' => '캐시 삭제 중...',
            'success' => '캐시가 삭제되었습니다.',
            'error' => '캐시 삭제에 실패했습니다.',
        ),
        'action' => function (&$data) {
            Cache::forget('pages');

            return true;
        },
    ),
);
```

세팅 페이지의 액션 콜백은 현재 저장된 설정 데이터를 참조로 전달받습니다. `true`를 반환하면 성공으로 처리되고, 문자열을 반환하면 커스텀 오류 메시지로 표시됩니다.

<a name="storage-path"></a>
## 저장 경로

```php {1}
'storage_path' => storage_path('administrator_settings'),
```

`storage_path`를 지정하지 않으면 현재 코드 기준 기본 저장 디렉터리는 `storage_path() . '/administrator_settings/'`입니다. 직접 지정하는 경우 실제 존재하는 디렉터리여야 합니다.

설정 페이지를 메뉴에 추가하는 방법은 [설정 문서의 메뉴 섹션](./configuration.md#menu)을 참고하십시오.
