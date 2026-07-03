# 필드 타입 - Number

`number`는 숫자 입력 필드입니다.

```php {2,4-7}
'price' => array(
    'type' => 'number',
    'title' => '가격',
    'symbol' => '₩',
    'decimals' => 0,
    'thousands_separator' => ',',
    'decimal_separator' => '.',
);
```

`symbol`은 입력창 주변 표시용 기호입니다. `decimals`, `thousands_separator`, `decimal_separator`로 표시 형식을 조정할 수 있습니다.

필터로 사용하면 최솟값과 최댓값 범위로 목록을 검색할 수 있습니다.
