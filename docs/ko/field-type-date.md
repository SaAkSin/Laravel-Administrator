# 필드 타입 - Date

- [사용법](#usage)
- [필터](#filter)

<a name="usage"></a>
## 사용법

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-date.png" />

`date` 필드 타입은 데이터베이스의 DATE 또는 DATETIME 타입이어야 합니다.

	'date' => array(
		'type' => 'date',
		'title' => 'Date',
		'date_format' => 'yy-mm-dd', // 선택 사항, 기본값은 이 값입니다.
	)

수정 폼에서 관리자 사용자에게 jQuery UI Datepicker가 제공됩니다.

`date_format` 옵션을 사용하면 날짜가 표시되는 방식을 정의할 수 있습니다. 이는 [jQuery Datepicker formatDate](http://docs.jquery.com/UI/Datepicker/formatDate)의 포맷팅 옵션을 사용합니다.

<a name="filter"></a>
## 필터

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-date-filter.png" />

`date` 필드 필터에는 시작 날짜와 종료 날짜가 함께 제공됩니다. 이를 통해 결과 집합을 특정 범위로 좁히거나, 최소 날짜만 설정하거나, 최대 날짜만 설정할 수 있습니다.
