# 필드 타입 - File

`file`은 파일 업로드 필드입니다. 데이터베이스에는 파일명이 저장되고, 실제 파일은 `location`에 저장됩니다.

```php {2,4,8}
'manual' => array(
    'type' => 'file',
    'title' => '매뉴얼 파일',
    'location' => storage_path('app/manuals'),
    'naming' => 'random',
    'length' => 20,
    'size_limit' => 2,
    'mimes' => 'pdf,doc,docx',
);
```

`naming`은 `random` 또는 `keep`을 사용할 수 있습니다. `size_limit`은 MB 단위의 업로드 제한이고, `mimes`는 Laravel mimes validation 형식을 따릅니다.
