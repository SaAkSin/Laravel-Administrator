# 필드 타입 - Textarea

`textarea`는 여러 줄 텍스트 입력 필드입니다.

```php {2,5}
'summary' => array(
    'type' => 'textarea',
    'title' => '요약',
    'limit' => 300,
    'height' => 130,
);
```

`limit`은 글자 수 제한이고, `height`는 입력 영역 높이를 픽셀 단위로 지정합니다.
