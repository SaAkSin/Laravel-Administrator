# 필드 타입 - Datetime

`datetime`은 날짜와 시간을 함께 입력하는 필드입니다. 데이터베이스 컬럼은 DATETIME 계열을 사용합니다.

```php {2,4-5}
'published_at' => array(
    'type' => 'datetime',
    'title' => '게시 일시',
    'date_format' => 'yy-mm-dd',
    'time_format' => 'HH:mm',
);
```

필터로 사용하면 시작 일시와 종료 일시 범위로 목록을 검색할 수 있습니다.
