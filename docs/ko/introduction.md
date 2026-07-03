# 소개

- [개요](#overview)
- [주요 특징](#features)
- [인증](#authentication)
- [Eloquent](#eloquent)
- [설정 페이지](#settings-pages)
- [다음 단계](#next)

<a name="overview"></a>
## 개요

Laravel Administrator는 Laravel 10 기반 프로젝트에서 Eloquent 모델과 운영 설정을 선언형 PHP 설정으로 관리하는 관리자 페이지 빌더입니다.

기존 FrozenNode Administrator를 기반으로 현대 Laravel 환경에 맞게 패키지 구조, PHP 요구 버전, Vite 기반 프론트엔드, Alpine.js/Tailwind CSS UI, Quill 기반 `wysiwyg2` 필드 등을 정비한 배포판입니다.

```php {3,6,10}
return array(
    'title' => '사용자',
    'model' => App\Models\User::class,
    'columns' => array(
        'name',
        'email',
    ),
    'edit_fields' => array(
        'name',
        'email',
    ),
);
```

<a name="features"></a>
## 주요 특징

- Laravel 10과 PHP 8.1 이상 지원
- 모델, 컬럼, 필드, 필터, 액션을 PHP 배열로 선언
- `belongsTo`, `belongsToMany`, `hasOne`, `hasMany` 관계 필드 지원
- 모델 목록 컬럼, 관계 컬럼, 커스텀 출력 포맷 지원
- 모델과 별개인 세팅 페이지 지원
- Vite, Alpine.js, Tailwind CSS 기반 관리자 UI
- CKEditor 4 기반 `wysiwyg`와 Quill 기반 `wysiwyg2` 필드 제공
- MySQL FULLTEXT 검색용 `fulltext_mysql`, 접두어 빠른 검색용 `text_quick` 필터 제공

<a name="authentication"></a>
## 인증

Administrator는 별도 인증 시스템을 제공하지 않습니다. 대신 기존 Laravel 인증과 권한 정책을 전역 `permission`, 모델별 `permission`, `action_permissions`로 연결합니다.

```php {2}
return array(
    'permission' => function () {
        return auth()->check() && auth()->user()->can('admin.access');
    },
);
```

전역 권한 검사는 실패하면 `login_path`로 리다이렉트합니다. 모델별 권한과 액션 권한은 해당 모델 화면과 버튼 노출에 반영됩니다.

<a name="eloquent"></a>
## Eloquent

Administrator는 Eloquent 모델을 전제로 동작합니다. 접근자, mutator, 관계 메서드, 모델 이벤트는 일반 Laravel 코드와 같은 방식으로 사용할 수 있습니다.

```php {3}
public function role()
{
    return $this->belongsTo(Role::class);
}
```

모델 화면 구성은 [모델 설정 문서](/docs/ko/model-configuration)를 참고하십시오.

<a name="settings-pages"></a>
## 설정 페이지

사이트 이름, 관리자 이메일, 로고, 캐시 설정처럼 특정 Eloquent 모델로 표현하기 어려운 값은 세팅 페이지로 관리할 수 있습니다.

```php {5-12}
return array(
    'title' => '사이트 설정',
    'edit_fields' => array(
        'site_name' => array(
            'title' => '사이트 이름',
            'type' => 'text',
        ),
        'maintenance_mode' => array(
            'title' => '점검 모드',
            'type' => 'bool',
        ),
    ),
);
```

세팅 페이지 작성법은 [세팅 설정 문서](/docs/ko/settings-configuration)를 참고하십시오.

<a name="next"></a>
## 다음 단계

처음 설치하는 경우 [설치 문서](/docs/ko/installation)를 먼저 확인하십시오. 전역 옵션은 [설정 문서](/docs/ko/configuration), 필드별 옵션은 [필드 문서](/docs/ko/fields)에서 확인할 수 있습니다.
