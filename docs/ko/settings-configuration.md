# 설정 구성 (Settings Configuration)

- [소개](#introduction)
- [예제](#examples)
- [옵션](#options)

<a name="introduction"></a>
## 소개

때로는 관리자를 위한 설정 페이지를 생성해야 할 때가 있습니다. [Eloquent 모델을 표시하는 페이지](/docs/model-configuration)와 마찬가지로 설정 페이지도 구성 파일로 표시됩니다. 이 파일들은 메인 `administrator.php` 설정의 [`settings_config_path`](/docs/configuration#settings-config-path) 옵션에 해당 위치의 경로를 제공하기만 하면 애플리케이션 디렉터리 구조 내 어디에나 둘 수 있습니다. 이 파일들의 이름은 `administrator.php` 설정의 [`menu`](/docs/configuration#menu) option에 제공된 값과 일치합니다.

> **참고**: 이 값들은 관리자 인터페이스에서 각 설정 페이지의 URI로도 사용됩니다.

설정 구성 파일이 정상적으로 작동하려면 반드시 제공해야 하는 몇 가지 필수 필드가 있으며, 여러 선택 필드도 있습니다. [아래의 옵션 목록을 참고하세요](#options).

설정은 스토리지의 하위 디렉터리인 `administrator_settings`에 JSON 파일로 저장됩니다. 하지만 설정 페이지가 이 JSON 파일들 중 하나에 저장되기 전에, 먼저 정의되어 있을 수 있는 [`rules`](#validation-rules)를 사용하여 유효성 검사를 거치며, 그 후 [`before_save`](#before-save) 함수로 전달되어 추가적인 유효성 검사를 실행하거나 원하는 방식(예: 데이터베이스에 기록하거나 PHP 구성 파일에 작성)으로 데이터를 저장하여 앱 내에서 자체적으로 사용할 수 있습니다.

<a name="examples"></a>
## 예제

몇 가지 예제 구성 파일은 [Administrator의 GitHub 리포지토리](https://github.com/FrozenNode/Laravel-Administrator/tree/master/examples)에 있는 `/examples` 디렉터리에서 확인하실 수 있습니다.

<a name="options"></a>
## 옵션

아래는 설정 페이지에 사용 가능한 모든 옵션 목록입니다. 필수 옵션은 *(필수)*로 표시되어 있습니다.

- [제목 (Title)](#title) *(필수)*
- [편집 필드 (Edit Fields)](#edit-fields) *(필수)*
- [유효성 검사 규칙 (Validation Rules)](#validation-rules)
- [저장 전 처리 (Before Save)](#before-save)
- [권한 (Permission)](#permission)
- [사용자 정의 액션 (Custom Actions)](#custom-actions)
- [저장 경로 (Storage Path)](#storage-path)

<a name="title"></a>
### 제목 (Title) *(필수)*

```php
	/**
	 * 설정 페이지 제목
	 *
	 * @type string
	 */
	'title' => '사이트 설정',
```

메뉴 및 페이지의 주 제목으로 사용되는 설정 페이지의 제목입니다.

<a name="edit-fields"></a>
### 편집 필드 (Edit Fields) *(필수)*

```php
	/**
	 * 편집 필드 배열
	 *
	 * @type array
	 */
	'edit_fields' => array(
		'site_name' => array(
			'title' => '이름',
			'type' => 'text',
		),
		'page_cache_lifetime' => array(
			'title' => '페이지 캐시 수명 (분 단위)',
			'type' => 'number',
		),
		'logo' => array(
			'title' => '이미지 (200 x 150)',
			'type' => 'image',
			'naming' => 'random',
			'location' => 'public/uploads/config/logo/originals/',
			'size_limit' => 2,
			'sizes' => array(
		 		array(200, 150, 'crop', 'public/uploads/config/logo/resize/', 100),
		 	)
		)
	),
```

`edit_fields` 배열을 사용하여 설정 페이지의 편집 가능한 필드들을 정의할 수 있습니다. key 및 관계(relationship) 필드를 제외한 모든 필드 타입이 허용됩니다. 이는 모델 구성 파일의 [`edit_fields`](/docs/model-configuration#edit-fields) 옵션과 매우 유사하게 작동합니다. 관리자가 설정 페이지를 저장하기로 결정하면, 모든 데이터가 포함된 배열이 [`before_save 콜백`](#before-save)으로 전달됩니다. 데이터 값의 인덱스는 `edit_fields` 배열에 정의한 인덱스와 동일합니다.

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/settings-overview.png" />

> 모든 편집 필드 타입과 옵션에 대한 자세한 설명은 **[필드 문서](/docs/fields)**를 참고하세요.

<a name="validation-rules"></a>
### 유효성 검사 규칙 (Validation Rules)

```php
	/**
	 * 라라벨(Laravel) 유효성 검사 클래스를 기반으로 한 폼 유효성 검사 규칙
	 *
	 * @type array
	 */
	'rules' => array(
		'site_name' => 'required|max:50',
		'site_email' => 'required|email',
	),
```

설정 페이지의 유효성 검사 규칙은 `rules` 옵션을 사용하여 정의할 수 있습니다. Administrator는 [라라벨 유효성 검사(Laravel's validation)](http://laravel.com/docs/validation)를 사용하여 모델을 검증합니다. 폼 데이터가 유효하지 않은 경우, 폼을 저장하지 않고 관리자에게 알림을 표시합니다.

<a name="before-save"></a>
### 저장 전 처리 (Before Save)

```php
	/**
	 * JSON 폼 데이터가 저장되기 전에 실행됩니다.
	 *
	 * @type function
	 * @param array		$data
	 *
	 * @return string (오류 시) / void (그 외)
	 */
	'before_save' => function(&$data)
	{
		if (today_is_tuesday())
		{
			return "죄송합니다. 화요일에는 사이트 설정을 저장할 수 없습니다.";
		}

		$data['site_name'] = $data['site_name'] . ' - The Blurst Site Ever';
	},
```

`before_save` 콜백은 [`rules`](#validation-rules) 옵션을 사용하는 기본 유효성 검사를 거친 후, 폼 데이터가 JSON 스토리지에 저장되기 전에 실행됩니다. 이 함수를 사용하여 데이터를 원하는 방식으로 저장할 수 있습니다. `$data` 매개변수는 참조(reference)로 전달되므로, 저장되기 전에 폼 데이터를 조작할 수도 있습니다.

`$data` 매개변수는 `key -> value` 쌍의 단순 배열입니다. 키는 [`edit_fields`](#edit-fields) 옵션에서 정의한 것과 동일합니다.

<a name="permission"></a>
## 권한 (Permission)

```php
	/**
	 * 권한(permission) 옵션은 인증 검사를 수행하는 클로저(closure)를 정의할 수 있게 해줍니다.
	 * 현재 사용자가 이 설정 페이지를 볼 수 있도록 허용하려면 true를 반환해야 합니다.
	 * "falsey" 성격의 모든 반환 값은 404 에러로 이어집니다.
	 *
	 * @type closure
	 */
	'permission'=> function()
	{
		return Auth::user()->has_role('developer');
	},
```

권한 옵션을 통해 현재 사용자가 이 설정 페이지에 액세스할 수 있는지 여부를 결정하는 클로저를 정의할 수 있습니다. 이 필드가 제공되는 경우(필수는 아님), 클로저가 참(truthy) 값을 반환할 때만 사용자에게 액세스 권한이 부여됩니다. 이 검사에 실패하면 사용자에게 404 에러가 반환됩니다.

<a name="custom-actions"></a>
## 사용자 정의 액션 (Custom Actions)

```php
	/**
	 * 설정 페이지의 사용자 정의 액션을 정의하는 곳입니다.
	 */
	'actions' => array(
		// 항목을 위로 순서 정렬
		'clear_page_cache' => array(
			'title' => '페이지 캐시 지우기',
			'messages' => array(
				'active' => '캐시를 지우는 중...',
				'success' => '페이지 캐시가 삭제되었습니다.',
				'error' => '페이지 캐시를 지우는 동안 오류가 발생했습니다.',
			),
			// 참(truthy) 반응이 반환되면 설정 데이터가 클로저로 전달되고 저장됩니다.
			'action' => function(&$data)
			{
				Cache::forget('pages');

				return true;
			}
		),

		// 데이터 API에서 현물 가격 가져오기
		'get_spot_prices' => array(...),
	),
```

관리자 사용자에게 사용자 정의 코드를 실행하는 버튼을 제공하려면 설정 페이지에 대해 사용자 정의 액션을 정의할 수 있습니다. 위 예제를 사용하면 다음과 같은 두 개의 버튼이 생성됩니다.

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/custom-actions-settings.png" />

사용자가 두 버튼 중 하나를 클릭하면 해당 버튼의 `action` 속성이 호출됩니다. 현재 저장된 설정 데이터는 `$data` 매개변수를 통해 함수에 참조(reference)로 전달됩니다. 즉, 데이터가 JSON에 저장되기 전에 원하는 방식으로 데이터를 변경할 수 있습니다.

> 사용자 정의 액션에 대한 자세한 정보는 **[액션 문서](/docs/actions)**를 참고하세요.

<a name="storage-path"></a>
### 저장 경로 (Storage Path)

```php
	/**
	 * 원본 설정 데이터를 저장할 스토리지 경로
	 *
	 * @type string
	 */
	'storage_path' => storage_path() . '/my_custom_directory',
```

선택적으로 `storage_path` 옵션을 제공하여 원본 설정 데이터가 저장될 디렉터리를 결정할 수 있습니다. 만약 `before_save` 콜백 내에서 다른 방식으로 데이터를 저장하고 있거나, 기본 위치(`/app/storage/administrator_settings`)를 사용하는 것에 만족하신다면 이 옵션을 생략해도 좋습니다.
