# 필드 타입 - Enum

- [사용법](#사용법)
- [필터](#필터)

<a name="사용법"></a>
## 사용법

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-enum.png" />

`enum` 필드 타입은 데이터베이스의 텍스트 계열 타입 또는 ENUM 타입이어야 합니다. 이 필드 타입은 절대 변경되지 않을 것이라 확신하는 데이터 세트 내에서 관리자 사용자의 선택 범위를 좁히는 데 도움이 됩니다. 계절의 이름 등이 이 필드를 활용하기에 적절한 예입니다.

```php
	'season' => array(
		'type' => 'enum',
		'title' => 'Season',
		'options' => array('Winter', 'Spring', 'Summer', 'Fall'), // 반드시 배열이어야 합니다.
	),
	// 다른 방법:
	'season' => array(
		'type' => 'enum',
		'title' => 'Season',
		'options' => array(
			'Winter' => 'Cold, Cold Winter!',
			'Spring',
			'Summer' => 'Hot, Hot Summer!',
			'Fall'
		),
	),
```

수정 폼에서 관리자 사용자에게는 선택 항목을 보여주는 셀렉트 박스가 제공됩니다.

`options` 옵션을 사용하면 사용자가 보게 될 선택 항목을 선언할 수 있습니다. 단순한 문자열 배열을 제공할 수도 있으며, 만약 배열의 키가 문자열인 경우 데이터베이스에는 키(key)가 저장되고 사용자에게는 값(value)이 표시됩니다.

<a name="필터"></a>
## 필터

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-enum-filter.png" />

`enum` 필드 필터는 기본적으로 수정 필드와 동일하게 작동합니다. 사용자에게 셀렉트 박스가 제공되며, 선택된 옵션에 따라 검색 결과의 범위를 좁혀 줍니다.
