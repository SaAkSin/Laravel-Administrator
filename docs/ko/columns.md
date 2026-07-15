# 컬럼

- [소개](#introduction)
- [단순 컬럼](#simple-columns)
- [컬럼 옵션](#column-options)
- [접근자 컬럼](#accessors)
- [커스텀 select](#custom-selects)
- [관계 컬럼](#relationship-columns)
- [커스텀 출력](#custom-outputs)

<a name="introduction"></a>
## 소개

`columns`는 모델 목록 테이블에 표시할 값을 정의합니다. 데이터베이스 컬럼, Eloquent 접근자, 관계 컬럼, 커스텀 select 값을 모두 사용할 수 있습니다.

```php {2-4}
'columns' => array(
    'id',
    'name',
    'email',
);
```

<a name="simple-columns"></a>
## 단순 컬럼

컬럼명을 문자열로 나열하면 해당 데이터베이스 컬럼을 그대로 표시합니다.

```php {2-4}
'columns' => array(
    'id',
    'name',
    'price',
);
```

옵션이 필요하면 컬럼명을 키로 두고 배열을 값으로 제공합니다.

```php {2}
'columns' => array(
    'id' => array(
        'title' => 'ID',
    ),
);
```

<a name="column-options"></a>
## 컬럼 옵션

### Title

```php
'email' => array(
    'title' => '이메일',
);
```

목록 헤더에 표시할 제목입니다.

### Visible

```php {3-5}
'secret_info' => array(
    'title' => '비공개 정보',
    'visible' => function ($model) {
        return auth()->user()->can('viewSecret', $model);
    },
);
```

`visible`은 컬럼 표시 여부를 결정합니다. 불리언 또는 클로저를 사용할 수 있습니다.

### Sortable

```php {4}
'image' => array(
    'title' => '이미지',
    'output' => '<img src="/uploads/products/resize/(:value)" height="100" />',
    'sortable' => false,
);
```

정렬이 불가능하거나 정렬 의미가 없는 컬럼은 `sortable`을 `false`로 지정합니다.

<a name="accessors"></a>
## 접근자 컬럼

Eloquent 접근자도 컬럼으로 사용할 수 있습니다.

```php {1}
public function getFormattedSalaryAttribute()
{
    return '₩' . number_format($this->getAttribute('salary'));
}
```

```php {2}
'columns' => array(
    'formatted_salary' => array(
        'title' => '급여',
    ),
);
```

접근자는 데이터베이스 컬럼이 아니므로 정렬하려면 `sort_field`를 지정합니다.

```php {4}
'columns' => array(
    'formatted_salary' => array(
        'title' => '급여',
        'sort_field' => 'salary',
    ),
);
```

<a name="custom-selects"></a>
## 커스텀 select

`select` 옵션을 사용하면 SQL 표현식으로 컬럼 값을 만들 수 있습니다. 현재 테이블을 참조할 때는 `(:table)` 플레이스홀더를 사용합니다.

```php {4}
'columns' => array(
    'is_good_label' => array(
        'title' => '상태',
        'select' => "IF((:table).is_good, '좋음', '나쁨')",
    ),
);
```

<a name="relationship-columns"></a>
## 관계 컬럼

관계 컬럼은 `relationship`에 Eloquent 관계 메서드 이름을 지정하고, `select`에 관계 테이블에서 가져올 값을 지정합니다.

```php {4-5}
'columns' => array(
    'role_name' => array(
        'title' => '역할',
        'relationship' => 'role',
        'select' => '(:table).name',
    ),
);
```

집계도 가능합니다.

```php {4-5}
'columns' => array(
    'posts_count' => array(
        'title' => '게시글 수',
        'relationship' => 'posts',
        'select' => 'COUNT((:table).id)',
    ),
);
```

중첩 관계와 관계 타입별 세부 동작은 [관계 컬럼 문서](./relationship-columns.md)를 참고하십시오.

<a name="custom-outputs"></a>
## 커스텀 출력

`output`은 화면에 표시할 HTML 또는 문자열을 제어합니다. 문자열에서는 `(:value)`가 실제 값으로 치환됩니다.

```php {4}
'columns' => array(
    'hex' => array(
        'title' => '색상',
        'output' => '<div style="background-color: (:value); width: 120px; height: 20px;"></div>',
    ),
);
```

클로저를 사용하면 현재 값과 모델을 함께 받을 수 있습니다.

```php {4-6}
'columns' => array(
    'hex' => array(
        'title' => '색상',
        'output' => function ($value, $model) {
            return '<span style="color: ' . e($value) . '">' . e($model->name) . '</span>';
        },
    ),
);
```

이미지 필드처럼 파일 경로를 표시해야 하는 컬럼에도 같은 방식으로 사용할 수 있습니다.

```php {4}
'columns' => array(
    'banner_image' => array(
        'title' => '배너',
        'output' => '<img src="/uploads/banners/resize/(:value)" height="80" />',
    ),
);
```
