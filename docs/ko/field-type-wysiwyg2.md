# 필드 타입 - WYSIWYG2

`wysiwyg2`는 Quill 기반 리치 텍스트 편집기입니다. 데이터베이스 컬럼은 TEXT 계열을 사용하십시오.

```php {2}
'body' => array(
    'type' => 'wysiwyg2',
    'title' => '본문',
);
```

필드 값은 HTML로 저장됩니다. 테이블, 소스 편집 등 CKEditor 4 기능이 필요한 화면에서는 [WYSIWYG](/docs/ko/field-type-wysiwyg)를 사용할 수 있습니다.
