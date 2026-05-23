# 필드 타입 - Time (시간)

- [사용법](#usage)
- [필터](#filter)

<a name="usage"></a>
## 사용법

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-time.png" />

`time` 필드 타입은 데이터베이스의 TIME 타입이어야 합니다.

	'start_time' => array(
		'type' => 'time',
		'title' => 'Start Time',
		'time_format' => 'HH:mm', // 선택 사항, 기본값으로 이 값이 설정됩니다.
	)

수정 폼(edit form)에서 관리자 사용자에게 jQuery timepicker(시간 선택기)가 제공됩니다.

`time_format` 옵션을 사용하면 시간이 표시되는 방식을 정의할 수 있습니다. 이는 [jQuery timepicker](http://trentrichardson.com/examples/timepicker/#tp-formatting)의 포맷 옵션을 사용합니다.

<a name="filter"></a>
## 필터

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-time-filter.png" />

`time` 필드 필터에는 시작 시간(start time)과 종료 시간(end time)이 함께 제공됩니다. 이를 통해 결과 세트를 특정 범위로 좁히거나, 최소 시간만 설정하거나, 최대 시간만 설정할 수 있습니다.
