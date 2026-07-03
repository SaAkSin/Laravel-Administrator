# 필드 타입 - Time

`time`은 시간 입력 필드입니다. 데이터베이스 컬럼은 TIME 계열을 사용합니다.

```php {2,4}
'start_time' => array(
    'type' => 'time',
    'title' => '시작 시간',
    'time_format' => 'HH:mm',
);
```

필터로 사용하면 시작 시간과 종료 시간 범위로 목록을 검색할 수 있습니다.
