# 필드 타입 - Bool

- [사용법](#usage)
- [필터](#filter)

<a name="usage"></a>
## 사용법

`bool` 필드 타입은 데이터베이스에서 정수(integer) 필드로 표현되어야 합니다. 일반적으로 스키마 생성 도구에서는 *BOOLEAN*을 선택할 수 있도록 지원하며, 이는 데이터베이스에서 *TINYINT(1)*과 같은 타입으로 변환됩니다. 이 필드는 데이터베이스 필드에 정수값 1과 0을 저장할 수만 있다면 정상적으로 작동합니다.

	'is_good' => array(
		'type' => 'bool',
		'title' => 'Is Good',
	)

수정 폼에서 관리자 사용자에게는 다음과 같은 체크박스가 표시됩니다:

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-bool.png" />

<a name="filter"></a>
## 필터

`bool` 필드 타입은 [`filters`](/docs/model-configuration#filters) 옵션에서 사용할 수 있습니다. 필터로 사용하면 관리자 사용자에게 참(true), 거짓(false) 또는 전체(all)를 선택할 수 있는 옵션이 제공됩니다.

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-bool-filter.png" />
