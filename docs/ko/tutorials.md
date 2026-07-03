# 튜토리얼 / 가이드

- [시작 순서](#getting-started)
- [예제 구성](#example)
- [참고 문서](#references)

<a name="getting-started"></a>
## 시작 순서

처음 적용하는 프로젝트에서는 다음 순서로 진행하는 것을 권장합니다.

1. [설치](/docs/ko/installation) 문서에 따라 Composer 설치와 publish를 실행합니다.
2. `administrator/settings` 디렉터리를 생성합니다.
3. `config/administrator.php`의 `menu`, `home_page`, `permission`을 프로젝트에 맞게 수정합니다.
4. 첫 모델 설정 파일을 `administrator/{name}.php`에 작성합니다.
5. `/admin` 경로에서 목록, 수정, 저장, 삭제 동작을 확인합니다.

<a name="example"></a>
## 예제 구성

```php {5,7-16,18-27}
<?php

return array(
    'title' => '게시글',
    'single' => '게시글',
    'model' => App\Models\Post::class,
    'columns' => array(
        'id',
        'title',
        'author_name' => array(
            'title' => '작성자',
            'relationship' => 'author',
            'select' => '(:table).name',
        ),
    ),
    'edit_fields' => array(
        'title' => array(
            'title' => '제목',
            'type' => 'text',
        ),
        'body' => array(
            'title' => '본문',
            'type' => 'wysiwyg2',
        ),
    ),
);
```

<a name="references"></a>
## 참고 문서

- [설정](/docs/ko/configuration)
- [모델 설정](/docs/ko/model-configuration)
- [필드](/docs/ko/fields)
- [컬럼](/docs/ko/columns)
- [액션](/docs/ko/actions)
