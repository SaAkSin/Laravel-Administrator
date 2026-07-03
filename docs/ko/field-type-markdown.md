# 필드 타입 - Markdown

`markdown`은 마크다운 원문을 입력하고 미리보기를 제공하는 필드입니다. 저장 값은 렌더링된 HTML이 아니라 마크다운 원문입니다.

```php {2,5}
'body' => array(
    'type' => 'markdown',
    'title' => '본문',
    'limit' => 3000,
    'height' => 220,
);
```

문서 작성 공간이 필요하므로 모델 설정의 `form_width`를 넓게 잡는 것을 권장합니다.
