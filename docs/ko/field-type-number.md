# 필드 타입 - Number (숫자)

- [사용법](#usage)
- [필터](#filter)

<a name="usage"></a>
## 사용법

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-number.png" />

`number` 필드 타입은 데이터베이스의 숫자형 타입이어야 합니다.

```php
	'price' => array(
		'type' => 'number',
		'title' => 'Price',
		'symbol' => '$', // 선택 사항, 기본값은 ''
		'decimals' => 2, // 선택 사항, 기본값은 0
		'thousands_separator' => ',', // 선택 사항, 기본값은 ','
		'decimal_separator' => '.', // 선택 사항, 기본값은 '.'
	)
```

수정 폼에서 관리자 사용자에게는 텍스트 입력 창이 제공됩니다. 이 텍스트 입력 창은 사용자가 올바른 형식의 숫자를 입력하도록 강제합니다.

`symbol` 옵션을 사용하면 숫자 앞에 기호를 설정할 수 있습니다. 이는 미적 목적을 위한 것이며 (위 그림처럼) 입력 창 외부에 표시됩니다.

`decimals` 옵션을 사용하면 숫자의 소수점 자리수(정밀도)를 설정할 수 있습니다.

`thousands_separator` 옵션을 사용하면 천 단위 구분 기호로 사용할 문자를 정의할 수 있습니다.

`decimal_separator` 옵션을 사용하면 소수점으로 사용할 문자를 정의할 수 있습니다.

<a name="filter"></a>
## 필터

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-number-filter.png" />

`number` 필터는 최솟값과 최댓값을 지원합니다. 이를 통해 최솟값과 최댓값을 모두 설정하여 결과 범위를 좁히거나, 최솟값만 설정하거나, 최댓값만 설정할 수 있습니다.
