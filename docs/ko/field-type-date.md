# 필드 타입 - Date

`date`는 날짜 입력 필드입니다. 데이터베이스 컬럼은 DATE 또는 DATETIME 계열을 사용합니다.

```php {2,4}
'published_on' => array(
    'type' => 'date',
    'title' => '게시일',
    'date_format' => 'yy-mm-dd',
);
```

필터로 사용하면 시작 날짜와 종료 날짜 범위로 목록을 검색할 수 있습니다.
