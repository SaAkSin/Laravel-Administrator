# 필드 타입 - WYSIWYG

`wysiwyg`는 패키지에 포함된 CKEditor 4 기반 리치 텍스트 편집기입니다. 데이터베이스 컬럼은 TEXT 계열을 사용하십시오.

```php {2}
'body' => array(
    'type' => 'wysiwyg',
    'title' => '본문',
);
```

필드 값은 HTML로 저장됩니다. 더 가벼운 Quill 기반 편집기를 선호하면 [WYSIWYG2](/docs/ko/field-type-wysiwyg2)를 사용하십시오.

WYSIWYG 필드는 화면 폭이 필요하므로 모델 설정의 `form_width`를 `400` 이상으로 조정하는 것을 권장합니다.
