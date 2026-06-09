# 필드 타입 - Key

- [사용법](#usage)
- [필터](#filter)

<a name="usage"></a>
## 사용법

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-key.jpg" />

`key` 필드 타입은 기본 키(primary key)의 값을 표시하는 데 사용됩니다. 기본 키 값은 데이터베이스 내부에서 처리되므로 이 필드는 편집 가능하게 설정할 수 없습니다.

	'id' => array(
		'type' => 'key', // 선택 사항... Administrator는 어떤 필드가 모델의 키인지 자동으로 감지합니다.
		'title' => 'ID',
	),

<a name="filter"></a>
## 필터

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-key-filter.jpg" />

`key` 필드 필터를 사용하면 찾고자 하는 항목의 키를 이미 알고 있는 경우 직접 입력하여 검색할 수 있습니다. 이는 데이터베이스의 다른 곳에서 참조되는 항목을 신속하게 찾고자 할 때 유용합니다.
