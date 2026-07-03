# 필드 타입 - Enum

`enum`은 정해진 선택지 중 하나를 고르는 필드입니다. 문자열 컬럼이나 DB ENUM 컬럼에 사용할 수 있습니다.

```php {2,5-8}
'status' => array(
    'type' => 'enum',
    'title' => '상태',
    'options' => array(
        'draft' => '임시 저장',
        'published' => '게시',
        'archived' => '보관',
    ),
);
```

배열 키가 문자열이면 키가 저장되고 값이 사용자에게 표시됩니다. 필터로도 사용할 수 있습니다.
