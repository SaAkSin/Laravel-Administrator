# 다국어 지원

- [소개](#introduction)
- [기본 로케일](#default-locale)
- [관리자 언어 선택](#administrator-locales)
- [설정 파일에서 번역 사용](#config-translation)
- [지원 언어](#available-languages)
- [언어 추가](#contributing)

<a name="introduction"></a>
## 소개

Administrator는 Laravel의 다국어 지원 기능을 그대로 사용합니다. 패키지 내부 번역 파일은 `src/lang/{locale}/administrator.php`와 `src/lang/{locale}/frontend.php`에 있으며, 애플리케이션의 기본 로케일과 Administrator 전역 설정의 `locales` 옵션에 따라 화면 언어가 결정됩니다.

<a name="default-locale"></a>
## 기본 로케일

Laravel 애플리케이션의 기본 언어는 `config/app.php`에서 설정합니다.

```php {2}
return array(
    'locale' => 'ko',
);
```

Laravel 10에서는 환경별로 관리하려면 `.env`와 config 값을 연결해 사용할 수 있습니다.

```dotenv
APP_LOCALE=ko
```

<a name="administrator-locales"></a>
## 관리자 언어 선택

관리자 화면 우측 상단에서 언어를 선택하게 하려면 `config/administrator.php`의 `locales`에 사용할 언어를 등록합니다.

```php {2}
return array(
    'locales' => array('ko', 'en', 'ja'),
);
```

`locales`가 비어 있으면 별도 언어 선택 메뉴를 표시하지 않습니다.

<a name="config-translation"></a>
## 설정 파일에서 번역 사용

전역 설정, 모델 설정, 세팅 설정 파일에서는 Laravel 번역 함수를 그대로 사용할 수 있습니다.

```php {3}
return array(
    'title' => __('admin.users.title'),
    'single' => __('admin.users.single'),
    'model' => App\Models\User::class,
    'columns' => array(
        'name' => array(
            'title' => __('admin.users.name'),
        ),
    ),
    'edit_fields' => array(
        'name' => array(
            'title' => __('admin.users.name'),
            'type' => 'text',
        ),
    ),
);
```

애플리케이션 번역 파일은 Laravel 표준 위치인 `lang/{locale}` 또는 `resources/lang/{locale}` 구조를 사용하십시오. 실제 위치는 프로젝트의 Laravel 버전과 설정에 따릅니다.

<a name="available-languages"></a>
## 지원 언어

현재 패키지에 포함된 언어 디렉터리는 다음과 같습니다.

```text
ar az bg ca da de en es eu fi fr hr hu it ja nb nl pl pt pt-BR ro ru se si sk sr tr uk vi zh-CN zh-TW
```

<a name="contributing"></a>
## 언어 추가

새 언어를 추가하려면 `src/lang/{locale}/administrator.php`와 `src/lang/{locale}/frontend.php`를 함께 추가합니다. 기존 `src/lang/en` 파일을 복사해 번역하면 구조를 맞추기 쉽습니다.

```text {2-3}
src/lang/ko/
  administrator.php
  frontend.php
```

번역 개선은 [GitHub 이슈](https://github.com/SaAkSin/Laravel-Administrator/issues) 또는 Pull Request로 제안할 수 있습니다. 기여 절차는 [기여하기 문서](/docs/ko/contributing)를 참고하십시오.
