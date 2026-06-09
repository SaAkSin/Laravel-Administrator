# 다국어 지원 (Localization)

- [소개](#introduction)
- [언어 설정하기](#setting-up-languages)
- [언어 변경하기](#changing-languages)
- [Administrator 설정 파일에서의 다국어 지원](#localization-in-administrator-config-file)
- [모델 설정 파일에서의 다국어 지원](#localization-in-model-config-files)
- [지원 가능한 언어](#available-languages)
- [기여하기 / 다른 언어 추가하기](#contributing)
- [사용된 플러그인](#plugins-used)

<a name="introduction"></a>
## 소개

Administrator는 다국어 지원(Localization)을 완벽하게 지원합니다. 현재는 몇 가지 언어만 제공되지만, [새로운 언어를 추가하는 방법도 매우 쉽습니다](#contributing). Administrator에 내장된 기본적인 다국어 지원 외에도, 관리자 설정 파일 내에서 다국어 지원을 직접 사용할 수 있습니다. 이는 Laravel의 다국어 지원 기능을 활용하므로, 설정 파일의 어느 곳에서든 `trans('some.item')` 또는 `Lang::get('some.item')` 구문을 사용하는 데 아무런 문제가 없습니다.

<a name="setting-up-languages"></a>
## 언어 설정하기

Administrator는 Laravel의 내장 다국어 지원 기능을 사용하므로, Administrator를 지역화하기 위해 해야 할 일은 `app/config/app.php` 파일에서 기본 로케일(default locale)을 변경하는 것이 전부입니다.

**기본 언어 변경하기**

	'locale' => 'de',

기본적으로 이 값은 `en`입니다.

**추가 언어 설정하기**

Laravel 3에서는 `application.php` 설정 파일에서 허용할 언어 배열을 제공할 수 있었습니다. L4(Laravel 4)에서는 이 기능이 제거되었으므로, `languages` 배열이 Administrator의 설정 파일(`app/config/packages/frozennode/administrator/administrator.php`)로 이동되었으며 현재는 `locales`라고 불립니다.

	'locales' => array('en', 'de', 'hu'),

기본적으로 이 값은 빈 배열 `array()`입니다.

본인의 언어가 지원되는지 확인하려면 [지원 가능한 언어](#available-languages) 목록을 참고하십시오.

<a name="changing-languages"></a>
## 언어 변경하기

만약 Administrator 설정 파일의 `locales` 배열에 둘 이상의 유효한 값을 제공하면, 관리자 페이지 우측 상단에 언어 선택기가 표시됩니다:

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/localization.png" />

Administrator는 이 항목들을 빌드하기 전에 기본 언어를 먼저 확인하므로, 기본 언어 URI를 사용하지 않더라도 사이트의 나머지 부분과 매끄럽게 연동됩니다.

<a name="localization-in-administrator-config-file"></a>
## Administrator 설정 파일에서의 다국어 지원

`app/config/packages/frozennode/administrator/administrator.php` 파일에서 다국어 지원을 설정하는 것은 매우 쉽습니다. 예를 들어, 관리자 인터페이스의 제목을 지역화하려면 다음과 같이 작성합니다:

	/**
	 * 페이지 제목
	 *
	 * @type string
	 */
	'title' => trans('administrator.title'),

언어 파일은 원하시는 대로 설정할 수 있지만, 이 예시의 경우에는 (예를 들어) `app/lang/en`, `app/lang/de` 또는 사용하고자 하는 모든 언어 디렉토리 아래에 `administrator.php` 파일을 생성해야 합니다. 해당 파일은 다음과 같이 작성됩니다:

	return array(
		"title"       => "Admin",
	);

이것은 단순한 [Laravel의 다국어 지원(Localization)](http://laravel.com/docs/localization) 기능이므로, 특별히 새로울 것은 없습니다!

<a name="localization-in-model-config-files"></a>
## 모델 설정 파일에서의 다국어 지원

모델 설정 파일에서도 특별한 예외는 없습니다... 따라서 표준 Laravel 다국어 지원 구문을 사용하여 언제든지 지역화를 수행할 수 있습니다!

<a name="available-languages"></a>
## 지원 가능한 언어

Administrator는 현재 다음과 같은 언어들을 지원합니다:

> ar az bg ca da de en es eu fi fr hr hu it ja nb nl pl pt pt-BR ru se si sk sr tr uk vi zh-CN zh-TW

목록에 원하는 언어가 없다면, [새로운 언어 기여를 매우 쉽게 진행할 수 있습니다](#contributing)!

<a name="contributing"></a>
## 기여하기 / 다른 언어 추가하기

Administrator의 언어 파일은 패키지의 `src/lang` 디렉토리에 있습니다. 각 언어는 현재 `administrator.php`와 `knockout.php`라는 두 개의 파일을 필요로 합니다. 새로운 언어를 추가하려면, 먼저 [문서의 기여(Contributing) 섹션](/docs/contributing)을 참고하십시오. 추가하고자 하는 언어 디렉토리 아래에 이 두 파일을 생성한 후 Pull Request를 보내주시면 됩니다. 만약 GitHub에서 포크(Fork)하여 Pull Request를 보내는 것이 번거로우시다면 걱정하지 마세요! 단순히 [새 이슈(Issue)를 생성](https://github.com/FrozenNode/Laravel-Administrator/issues)하고 그곳에 번역본을 작성해 주셔도 됩니다. 저희가 확인하여 `lang` 디렉토리에 추가하도록 하겠습니다.

<a name="plugins-used"></a>
## 사용된 플러그인

Administrator는 자체적으로 방대한 다국어 지원을 제공하는 여러 플러그인을 사용하고 있습니다. Administrator는 사용자가 제공한 Laravel 언어 설정과 플러그인의 언어를 자동으로 맞추려고 시도하지만, 일부 플러그인은 해당 언어를 완전히 지원하지 않을 수도 있습니다. 다음 플러그인들의 언어 지원 여부를 확인할 수 있습니다:

**[CKEditor](http://ckeditor.com/)**
wysiwyg 필드 타입에서 사용됩니다. Administrator 내 경로: `/public/js/ckeditor/lang/`

**[jQueryUI DatePicker](http://jqueryui.com/datepicker/)**
date 필드 타입에서 사용됩니다. Administrator 내 경로: `/public/js/jquery/i18n/`

**[jQuery TimePicker](http://jonthornton.github.com/jquery-timepicker/)**
time 필드 타입에서 사용됩니다. Administrator 내 경로: `/public/js/jquery/localization/`


