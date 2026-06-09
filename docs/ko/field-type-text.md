# 필드 타입 - Text (텍스트)

- [사용법](#usage)
- [필터](#filter)

<a name="usage"></a>
## 사용법

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-text.png" />

`text` 필드 타입은 데이터베이스의 텍스트와 유사한 모든 타입에 사용할 수 있습니다. `text`는 기본 필드 타입이므로 `type` 속성을 설정하지 않아도 무방합니다.

	'name' => array(
		'type' => 'text', // 선택 사항, 기본값은 'text'입니다.
		'title' => '이름',
		'limit' => 30, // 선택 사항, 기본값은 제한이 없습니다.
	)

수정 폼(Edit form)에서 관리자 사용자는 간단한 텍스트 입력 창을 제공받게 됩니다.

`limit` 옵션을 사용하면 해당 필드의 글자 수 제한을 설정할 수 있습니다.

<a name="filter"></a>
## 필터

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-text-filter.png" />

`text` 필드 필터를 사용하면 해당 필드에서 제공된 문자열과 일치하는 항목을 검색할 수 있습니다.
