# 모델 설정

- [소개](#introduction)
- [파일 위치](#location)
- [기본 예제](#example)
- [필수 옵션](#required-options)
- [조회와 필터](#query-and-filters)
- [권한](#permission)
- [커스텀 액션](#custom-actions)
- [유효성 검사](#validation)
- [화면 옵션](#view-options)

<a name="introduction"></a>
## 소개

모델 설정 파일은 하나의 Eloquent 모델을 Administrator 화면으로 노출하는 PHP 배열입니다. 모델 목록의 컬럼, 수정 폼의 필드, 필터, 권한, 액션을 모두 이 파일에서 선언합니다.

현재 버전에서는 Laravel 설정 캐시와 중복 함수 선언 문제를 피하기 위해 파일 최상단에서 곧바로 `return array(...);`를 사용하는 방식을 권장합니다.

<a name="location"></a>
## 파일 위치

모델 설정 파일은 `config/administrator.php`의 `model_config_path`에 둡니다. 기본값은 프로젝트 루트의 `administrator/`입니다.

```php {2,5}
return array(
    'model_config_path' => base_path('administrator'),
    'menu' => array(
        'users',
    ),
);
```

위 설정에서는 `administrator/users.php` 파일이 필요하며, `/admin/users` 화면과 연결됩니다.

<a name="example"></a>
## 기본 예제

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

<a name="required-options"></a>
## 필수 옵션

### Title

```php
'title' => '사용자',
```

관리자 메뉴와 목록 화면에서 사용할 복수형 또는 화면 제목입니다.

### Single

```php
'single' => '사용자',
```

새 항목 생성 버튼, 삭제 확인 메시지처럼 단수형 표현이 필요한 곳에 사용됩니다.

### Model

```php {1}
'model' => App\Models\User::class,
```

관리할 Eloquent 모델의 정규화된 클래스 이름입니다.

### Columns

```php {2-4,8-10}
'columns' => array(
    'id',
    'name',
    'email',
    'role_name' => array(
        'title' => '역할',
        'relationship' => 'role',
        'select' => '(:table).name',
    ),
);
```

목록 테이블에 표시할 컬럼입니다. 문자열만 넣으면 컬럼명을 그대로 사용하고, 배열을 넣으면 제목, 관계, 정렬, 출력 형식을 세밀하게 지정할 수 있습니다. 자세한 내용은 [컬럼 문서](/docs/ko/columns)를 참고하십시오.

### Edit Fields

```php {2,7,12}
'edit_fields' => array(
    'name' => array(
        'title' => '이름',
        'type' => 'text',
    ),
    'role' => array(
        'title' => '역할',
        'type' => 'relationship',
        'name_field' => 'name',
    ),
    'is_active' => array(
        'title' => '활성',
        'type' => 'bool',
    ),
);
```

수정 폼에 표시할 필드입니다. 지원 필드 타입은 [필드 문서](/docs/ko/fields)와 각 필드 타입 문서를 참고하십시오.

<a name="query-and-filters"></a>
## 조회와 필터

### Filters

```php {2,6,10}
'filters' => array(
    'name' => array(
        'title' => '이름',
        'type' => 'text',
    ),
    'email' => array(
        'title' => '이메일 빠른 검색',
        'type' => 'text_quick',
    ),
    'bio' => array(
        'title' => '자기소개 전문 검색',
        'type' => 'fulltext_mysql',
    ),
);
```

`filters`는 목록 검색 영역에 표시할 필드입니다. 기본 필드 외에 현재 패키지는 접두어 검색용 `text_quick`, MySQL `MATCH ... AGAINST` 기반의 `fulltext_mysql` 필터도 제공합니다.

### Query Filter

```php {1}
'query_filter' => function ($query) {
    if (! auth()->user()->can('users.view_all')) {
        $query->where('team_id', auth()->user()->team_id);
    }
},
```

`query_filter`는 Administrator가 목록 쿼리를 실행하기 전에 호출됩니다. 현재 사용자의 권한이나 테넌트 범위에 따라 목록을 제한할 때 사용합니다.

### Sort

```php
'sort' => array(
    'field' => 'created_at',
    'direction' => 'desc',
),
```

기본 정렬 컬럼과 방향을 지정합니다. `field`는 `columns`에 포함된 컬럼이어야 하며, `direction`은 `asc` 또는 `desc`를 사용합니다.

<a name="permission"></a>
## 권한

### Permission

```php
'permission' => function () {
    return auth()->user()->can('users.access');
},
```

해당 모델 화면을 볼 수 있는지 결정합니다. 문자열 콜백도 사용할 수 있습니다.

```php
'permission' => 'App\Admin\Permissions\UserPermission@access',
```

### Action Permissions

```php {2-5}
'action_permissions' => array(
    'create' => true,
    'view' => true,
    'update' => function ($model) {
        return auth()->user()->can('update', $model);
    },
    'delete' => function ($model) {
        return auth()->user()->can('delete', $model);
    },
);
```

기본 액션은 `create`, `view`, `update`, `delete`입니다. 각 값은 불리언, 클로저, 문자열 콜백으로 지정할 수 있습니다.

<a name="custom-actions"></a>
## 커스텀 액션

### 개별 항목 액션

```php {5,13}
'actions' => array(
    'activate' => array(
        'title' => '활성화',
        'messages' => array(
            'active' => '활성화 중...',
            'success' => '활성화되었습니다.',
            'error' => '활성화에 실패했습니다.',
        ),
        'action' => function ($model) {
            $model->forceFill(array('is_active' => true))->save();

            return true;
        },
    ),
);
```

`actions`는 선택한 모델 항목에 대해 실행됩니다. `action` 콜백은 해당 Eloquent 모델을 전달받습니다.

### 전역 액션

```php {2,8}
'global_actions' => array(
    'export' => array(
        'title' => 'CSV 다운로드',
        'messages' => array(
            'active' => '파일 생성 중...',
            'success' => '파일이 생성되었습니다.',
            'error' => '파일 생성에 실패했습니다.',
        ),
        'action' => function ($query) {
            $rows = $query->get();

            return response()->streamDownload(function () use ($rows) {
                // CSV 출력
            }, 'users.csv');
        },
    ),
);
```

`global_actions`는 현재 필터가 적용된 쿼리 빌더를 전달받습니다.

<a name="validation"></a>
## 유효성 검사

### Form Request

```php {1}
'form_request' => App\Http\Requests\Admin\UserSaveRequest::class,
```

저장 요청을 Laravel Form Request로 검증합니다. 저장 전 컨트롤러에서 Form Request 오류를 수집해 JSON 응답으로 반환합니다.

### Rules

```php
'rules' => array(
    'name' => 'required|string|max:255',
    'email' => 'required|email',
);
```

### Messages

```php
'messages' => array(
    'name.required' => '이름을 입력하십시오.',
    'email.email' => '올바른 이메일 주소를 입력하십시오.',
);
```

`rules`와 `messages`는 Laravel validation 규칙과 동일한 형식을 사용합니다. 모델에 정적 `$rules`, `$messages` 배열을 정의해도 사용할 수 있지만, 설정 파일의 값이 우선합니다.

<a name="view-options"></a>
## 화면 옵션

### Form Width

```php
'form_width' => 500,
```

수정 폼 영역의 너비를 조정합니다. 기본값은 `285`입니다.

### Link

```php
'link' => function ($model) {
    return route('users.show', $model);
},
```

수정 폼 상단에 프론트엔드 상세 페이지로 이동하는 링크를 표시합니다.

### View

```php
'view' => true,
```

데이터베이스 VIEW 기반 모델처럼 조회 중심 모델을 다룰 때 사용할 수 있는 옵션입니다. 현재 코드는 불리언 값으로 검증합니다.

### Top Actions

```php
'is_top_actions' => true,
```

액션 UI를 상단 영역 중심으로 배치해야 하는 화면에서 사용할 수 있는 옵션입니다. 현재 코드는 불리언 값으로 검증합니다.

더 자세한 필드 작성법은 [필드 문서](/docs/ko/fields), 관계 컬럼 작성법은 [관계 컬럼 문서](/docs/ko/relationship-columns), 커스텀 액션 작성법은 [액션 문서](/docs/ko/actions)를 참고하십시오.
