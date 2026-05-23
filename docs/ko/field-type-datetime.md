# 필드 타입 - Datetime (일시)

- [사용법](#usage)
- [필터](#filter)

<a name="usage"></a>
## 사용법

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-datetime.png" />

`datetime` 필드 타입은 데이터베이스의 DATETIME 타입이어야 합니다.

	'start_time' => array(
		'type' => 'datetime',
		'title' => '시작 시간',
		'date_format' => 'yy-mm-dd', // 선택 사항, 기본값은 이 값으로 설정됩니다.
		'time_format' => 'HH:mm', 	 // 선택 사항, 기본값은 이 값으로 설정됩니다.
	)

편집 폼에서 관리자 사용자에게는 jQuery datetimepicker가 제공됩니다.

`date_format` 옵션은 날짜가 표시되는 방식을 정의할 수 있게 해줍니다. 이 옵션은 [jQuery Datepicker formatDate](http://docs.jquery.com/UI/Datepicker/formatDate)의 포맷 옵션을 사용합니다.

`time_format` 옵션은 시간이 표시되는 방식을 정의할 수 있게 해줍니다. 이 옵션은 [jQuery timepicker](http://trentrichardson.com/examples/timepicker/#tp-formatting)의 포맷 옵션을 사용합니다.

<a name="filter"></a>
## 필터

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-datetime-filter.png" />

`datetime` 필드 필터는 시작 일시와 종료 일시를 제공합니다. 이를 통해 결과 집합을 특정 범위로 좁히거나, 최소 일시만 설정하거나, 최대 일시만 설정할 수 있습니다.
