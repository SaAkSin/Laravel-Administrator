# 필드

- [소개](#introduction)
- [공통 옵션](#common-options)
- [표시와 편집 제어](#visibility)
- [기본값과 설명](#defaults)
- [필터](#filters)
- [설정 페이지](#settings-page)
- [지원 필드 타입](#field-types)

<a name="introduction"></a>
## 소개

`edit_fields`는 모델 또는 세팅 수정 폼에 표시할 입력 필드를 정의합니다. 모델 설정에서는 Eloquent 모델의 실제 컬럼이나 관계 메서드 이름을 키로 사용하고, 세팅 설정에서는 저장할 설정 키를 그대로 사용합니다.

```php {2,6,10,15}
'edit_fields' => array(
    'name' => array(
        'title' => '이름',
        'type' => 'text',
    ),
    'is_active' => array(
        'title' => '활성',
        'type' => 'bool',
    ),
    'role' => array(
        'title' => '역할',
        'type' => 'relationship',
        'name_field' => 'name',
    ),
    'profile_image' => array(
        'title' => '프로필 이미지',
        'type' => 'image',
        'naming' => 'random',
        'location' => public_path('uploads/users/originals'),
    ),
);
```

문자열만 전달하면 `text` 필드로 간주하고 제목은 필드명으로 설정합니다.

```php {2}
'edit_fields' => array(
    'name',
);
```

<a name="common-options"></a>
## 공통 옵션

### Title

```php
'name' => array(
    'title' => '이름',
),
```

필드 라벨입니다. 생략하면 필드 키를 제목으로 사용합니다.

### Type

```php {3}
'hex' => array(
    'title' => '색상',
    'type' => 'color',
),
```

필드 타입입니다. 생략하면 기본값은 `text`입니다. 모델의 기본 키와 같은 이름의 필드는 `key` 타입으로 처리됩니다.

<a name="visibility"></a>
## 표시와 편집 제어

### Editable

```php {3}
'email' => array(
    'title' => '이메일',
    'editable' => false,
),
```

`editable`이 `false`이면 값을 보여주지만 수정할 수 없습니다. 클로저를 사용하면 모델 상태에 따라 편집 가능 여부를 결정할 수 있습니다.

```php {3-5}
'email' => array(
    'title' => '이메일',
    'editable' => function ($model) {
        return ! $model->exists;
    },
),
```

### Visible

```php {4-6}
'published_at' => array(
    'title' => '게시일',
    'type' => 'datetime',
    'visible' => function ($model) {
        return $model->exists;
    },
),
```

`visible`은 필드 표시 여부를 제어합니다. 기본값은 `true`입니다.

### Setter

```php {3}
'password' => array(
    'title' => '비밀번호',
    'setter' => true,
),
```

`setter`가 `true`인 필드는 Eloquent 모델에 값을 전달하지만 저장 직전에 일반 속성에서는 제거됩니다. 비밀번호처럼 mutator에서 가공할 값을 받을 때 유용합니다. `password` 필드는 기본적으로 setter로 동작합니다.

<a name="defaults"></a>
## 기본값과 설명

### Value

```php {4}
'status' => array(
    'title' => '상태',
    'type' => 'enum',
    'value' => 'draft',
    'options' => array(
        'draft' => '임시 저장',
        'published' => '게시',
    ),
),
```

새 항목 생성 시 기본값이나 필터의 초기값으로 사용됩니다.

### Description

```php {3}
'slug' => array(
    'title' => 'URL 슬러그',
    'description' => '비워두면 제목에서 자동 생성됩니다.',
),
```

입력 필드 하단에 도움말 문구를 표시합니다.

<a name="filters"></a>
## 필터

모델 설정의 `filters`는 목록 검색 영역에 표시할 필드를 정의합니다.

```php {2,6,10,14}
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
    'role' => array(
        'title' => '역할',
        'type' => 'relationship',
        'name_field' => 'name',
    ),
);
```

필터에 사용할 수 있는 대표 타입은 `key`, `text`, `text_quick`, `fulltext_mysql`, `number`, `bool`, `enum`, `date`, `time`, `datetime`, `relationship`입니다.

`text_quick`은 입력값으로 시작하는 레코드를 `LIKE '값%'` 조건으로 검색합니다. `fulltext_mysql`은 MySQL FULLTEXT 인덱스가 있는 컬럼에서 `MATCH ... AGAINST` 조건을 사용합니다.

필터 기본값도 `value`, `min_value`, `max_value`로 지정할 수 있습니다.

```php {4,10-11}
'filters' => array(
    'name' => array(
        'title' => '이름',
        'value' => 'Kim',
    ),
    'price' => array(
        'title' => '가격',
        'type' => 'number',
        'symbol' => '₩',
        'min_value' => 10000,
        'max_value' => 50000,
    ),
);
```

<a name="settings-page"></a>
## 설정 페이지

세팅 설정 파일에서는 `key`와 관계 필드(`belongs_to`, `belongs_to_many`, `has_one`, `has_many`)를 제외한 타입을 사용할 수 있습니다. 세팅 페이지 작성법은 [세팅 설정 문서](./settings-configuration.md)를 참고하십시오.

<a name="field-types"></a>
## 지원 필드 타입

현재 필드 팩토리에서 제공하는 타입은 다음과 같습니다.

| 타입 | 용도 |
| --- | --- |
| `key` | 모델 기본 키 |
| `text`, `text_quick`, `textarea` | 문자열 입력 및 검색 |
| `wysiwyg`, `wysiwyg2`, `markdown` | 리치 텍스트와 마크다운 편집 |
| `password` | 비밀번호 입력 |
| `date`, `time`, `datetime` | 날짜와 시간 입력 |
| `number` | 숫자 입력 |
| `bool` | 참/거짓 입력 |
| `enum` | 선택 목록 |
| `image`, `file` | 업로드 |
| `color` | 색상 입력 |
| `fulltext_mysql` | MySQL FULLTEXT 검색 필터 |
| `relationship` | Eloquent 관계 입력 |

관계 필드의 세부 옵션은 [관계 필드 문서](./field-type-relationship.md)를 참고하십시오.
