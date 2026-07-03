# 필드 타입 - Relationship

- [소개](#overview)
- [기본 사용법](#basic)
- [다중 관계](#many)
- [자동완성](#autocomplete)
- [옵션 필터링](#options-filter)
- [관계 제약 조건](#constraints)

<a name="overview"></a>
## 소개

`relationship` 필드는 Eloquent 관계를 관리자 폼에서 선택할 수 있게 합니다. 설정 파일에는 항상 `type => 'relationship'`로 작성하지만, 런타임에서는 실제 Eloquent 관계 타입에 따라 `belongs_to`, `belongs_to_many`, `has_one`, `has_many`로 분기됩니다.

관계 필드의 키는 Eloquent 모델에 정의된 관계 메서드 이름과 같아야 합니다.

<a name="basic"></a>
## 기본 사용법

```php {2,5}
'role' => array(
    'type' => 'relationship',
    'title' => '역할',
    'name_field' => 'name',
);
```

```php {3}
public function role()
{
    return $this->belongsTo(Role::class);
}
```

`name_field`는 선택 목록에 표시할 관계 모델의 컬럼 또는 접근자 이름입니다.

<a name="many"></a>
## 다중 관계

`belongsToMany` 관계도 같은 방식으로 설정합니다.

```php {2,5-6}
'tags' => array(
    'type' => 'relationship',
    'title' => '태그',
    'name_field' => 'name',
    'options_sort_field' => 'name',
    'options_sort_direction' => 'asc',
);
```

```php {3}
public function tags()
{
    return $this->belongsToMany(Tag::class);
}
```

피벗 테이블의 정렬 컬럼을 사용해 선택된 항목의 순서를 저장하려면 `sort_field`를 지정합니다.

```php {5}
'tags' => array(
    'type' => 'relationship',
    'title' => '태그',
    'name_field' => 'name',
    'sort_field' => 'ordering',
);
```

<a name="autocomplete"></a>
## 자동완성

관계 대상 데이터가 많은 경우 `autocomplete`를 사용해 검색 시점에 일부 항목만 조회합니다.

```php {5-7}
'users' => array(
    'type' => 'relationship',
    'title' => '사용자',
    'name_field' => 'name',
    'autocomplete' => true,
    'num_options' => 10,
    'search_fields' => array('name', 'email'),
);
```

`search_fields`는 `LIKE` 검색에 사용할 컬럼 또는 SQL 표현식 배열입니다. 생략하면 `name_field`를 기준으로 검색합니다.

<a name="options-filter"></a>
## 옵션 필터링

`options_filter`로 선택 가능한 관계 항목을 제한할 수 있습니다.

```php {5-7}
'users' => array(
    'type' => 'relationship',
    'title' => '활성 사용자',
    'name_field' => 'name',
    'options_filter' => function ($query) {
        $query->where('is_active', true);
    },
);
```

<a name="constraints"></a>
## 관계 제약 조건

두 관계 필드가 서로 연결되어 있다면 `constraints`로 한 필드의 선택값에 따라 다른 필드의 후보를 제한할 수 있습니다.

```php {8-13}
'edit_fields' => array(
    'country' => array(
        'title' => '국가',
        'type' => 'relationship',
        'name_field' => 'name',
    ),
    'state' => array(
        'title' => '주',
        'type' => 'relationship',
        'name_field' => 'name',
        'constraints' => array('country' => 'states'),
    ),
);
```

위 예제에서 `country`는 현재 모델의 관계 필드 이름이고, `states`는 선택된 Country 모델에 정의된 관계 메서드 이름입니다.

```php {3,8}
class Country extends Model
{
    public function states()
    {
        return $this->hasMany(State::class);
    }
}
```

`belongsToMany`끼리 연결된 관계에도 같은 방식으로 적용할 수 있습니다.
