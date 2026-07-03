# 필드 타입 - Image

`image`는 이미지 업로드 필드입니다. 데이터베이스에는 이미지 파일명이 저장되고, 원본과 리사이즈 이미지는 지정한 경로에 저장됩니다.

```php {2,4,9-12}
'profile_image' => array(
    'type' => 'image',
    'title' => '프로필 이미지',
    'location' => public_path('uploads/users/originals'),
    'naming' => 'random',
    'length' => 20,
    'size_limit' => 2,
    'display_raw_value' => false,
    'sizes' => array(
        array(120, 120, 'crop', public_path('uploads/users/thumbs'), 90),
        array(640, 480, 'fit', public_path('uploads/users/preview'), 90),
    ),
);
```

`sizes`의 형식은 `array(가로, 세로, 조정 방식, 저장 경로, 품질)`입니다. 조정 방식은 `exact`, `portrait`, `landscape`, `fit`, `auto`, `crop`을 사용할 수 있습니다.
