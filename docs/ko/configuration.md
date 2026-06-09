# 설정 (Configuration)

- [소개](#introduction)
- [옵션](#options)

<a name="introduction"></a>
## 소개

패키지가 설치되면, 다음 명령어를 통해 설정 파일을 퍼블리시(게시)할 수 있습니다:

### Laravel 5
    php artisan vendor:publish --provider='SaAkSin\Administrator\AdministratorServiceProvider'

### Laravel 4
	php artisan config:publish frozennode/administrator

이 명령을 실행하면 `app/config/packages/saaksin/administrator/administrator.php` 파일이 생성되고 몇 가지 기본값으로 채워집니다. 이 [설정 파일](http://administrator.frozennode.com/docs/configuration)은 Administrator와 상호작용하는 가장 기본적인 방법입니다.

모든 설정 옵션이 사용되지만, 모든 옵션을 반드시 제공해야 하는 것은 아닙니다.

<a name="options"></a>
## 옵션

사용 가능한 모든 옵션의 목록은 다음과 같습니다:

- [Uri](#uri)
- [도메인 (Domain)](#domain)
- [미들웨어 (Middleware)](#middleware)
- [타이틀 (Title)](#title)
- [모델 설정 경로 (Model Config Path)](#model-config-path)
- [설정파일 설정 경로 (Settings Config Path)](#settings-config-path)
- [메뉴 (Menu)](#menu)
- [권한 (Permission)](#permission)
- [대시보드 사용 여부 (Use Dashboard)](#use-dashboard)
- [대시보드 뷰 (Dashboard View)](#dashboard-view)
- [홈페이지 (Home Page)](#home-page)
- [사이트 돌아가기 경로 (Back To Site Path)](#back-to-site-path)
- [로그인 경로 (Login Path)](#login-path)
- [로그아웃 경로 (Logout Path)](#logout-path)
- [리다이렉트 키 (Redirect Key)](#redirect-key)
- [페이지당 글로벌 행 수 (Global Rows Per Page)](#global-rows-per-page)
- [로케일 (Locales)](#locales)

<a name="uri"></a>
### Uri

	/**
	 * 패키지 URI
	 *
	 * @type string
	 */
	'uri' => 'admin',

이 값은 Administrator 패키지를 호출할 기본 라우트(URI) 경로입니다.

<a name="domain"></a>
### Domain

	/**
	 * 페이지 도메인
	 *
	 * @type string
	 */
	'domain' => '',

이 값은 Administrator 라우트의 기본 도메인입니다. 이를 사용하여 관리자 페이지를 특정 도메인 또는 서브도메인으로만 제한할 수 있습니다.

<a name="middleware"></a>
### Middleware

	/**
	 * 관리자 라우트 추가 미들웨어
	 *
	 * @type array
	 */
	'middleware' => '',

이 값은 Administrator 라우트에서 추가로 실행하려는 미들웨어 목록입니다.

<a name="title"></a>
### Title

	/**
	 * 페이지 타이틀
	 *
	 * @type string
	 */
	'title' => 'Admin',

이 값은 페이지의 왼쪽 상단에 표시되어 사용자에게 보여지는 관리자 인터페이스의 제목입니다.

<a name="model-config-path"></a>
### Model Config Path

	/**
	 * 모델 설정 디렉토리 경로
	 *
	 * @type string
	 */
	'model_config_path' => app('path') . '/config/administrator',

이 값은 애플리케이션의 모델 설정 파일들이 위치할 디렉토리 경로입니다. 애플리케이션 설정 폴더 내에 `administrator`라는 하위 디렉토리를 만드는 것을 권장하지만, 원하는 위치 어디든 지정할 수 있습니다.

<a name="settings-config-path"></a>
### Settings Config Path

	/**
	 * 설정(Settings) 파일 디렉토리 경로
	 *
	 * @type string
	 */
	'settings_config_path' => app('path') . '/config/administrator/settings',

이 값은 애플리케이션의 설정(Settings) 파일들이 위치할 디렉토리 경로입니다. 관리자용 설정 페이지를 만들고자 한다면, 각 설정 관련 파일을 위의 경로에 저장하게 됩니다. `model_config_path`와 마찬가지로, 이 디렉토리를 어떻게 구성할지는 여러분의 선택입니다. 위의 경로가 권장하는 방식이지만, 본인의 프로젝트에 더 적합하고 합리적인 방식으로 설정 디렉토리를 구성하실 수 있습니다.

<a name="menu"></a>
### Menu

	/**
	 * 사이트의 메뉴 구조입니다. 모델의 경우, 모델 설정 파일의 이름이나 모델 설정 파일 이름들로 구성된
	 * 배열을 제공해야 합니다. 설정(Settings) 파일도 동일하게 적용되지만, 설정 파일 이름 앞에 반드시 'settings.'를
	 * 붙여야 합니다. 뷰(View) 경로 앞에 'page.'를 붙여 커스텀 페이지를 추가할 수도 있습니다.
	 * 이름 배열을 제공함으로써 특정 모델이나 설정 페이지들을 하나로 묶어 그룹화할 수 있습니다.
	 * 각 이름은 모델 설정 경로 또는 설정 파일 경로에 있는 동일한 이름의 설정 파일이거나,
	 * 정규화된 라라벨 뷰(Laravel view) 경로여야 합니다.
	 * 예를 들어, 'users'는 모델 설정 경로에 'users.php' 파일이 있어야 하고,
	 * 'settings.site'는 설정 파일 경로에 'site.php' 파일이 필요하며,
	 * 'page.foo.test'는 뷰 디렉토리 안의 'foo' 디렉토리에 'test.php' 또는 'test.blade.php' 파일이 있어야 합니다.
	 *
	 * @type array
	 *
	 * 	array(
	 *		'E-Commerce' => array('collections', 'products', 'product_images', 'orders'),
	 *		'homepage_sliders',
	 *		'users',
	 *		'roles',
	 *		'colors',
	 *		'Settings' => array('settings.site', 'settings.ecommerce', 'settings.social'),
	 * 		'Analytics' => array('E-Commerce' => 'page.ecommerce.analytics'),
	 *	)
	 */
	'menu' => array(),

메뉴 옵션은 사이트의 메뉴 구조를 설정하는 곳입니다. 서브메뉴를 원치 않는 경우, 모델 설정 파일이나 설정 파일의 이름을 문자열로 간단히 제공하면 됩니다. 이 값은 모델 또는 설정 파일의 PHP 파일 이름과 정확히 일치해야 합니다 (리눅스 환경을 사용 중이라면 대소문자를 구분한다는 의미입니다).

따라서 위의 예시의 경우, (`model_config_path`로 지정한 디렉토리 내에) `collections.php`, `orders.php`, `products.php`, `product_images.php`, `users.php`, `roles.php`, `colors.php`라는 이름의 설정 파일들이 있어야 합니다.

또한 (`settings_config_path`로 지정한 디렉토리 내에) `site.php`, `ecommerce.php`, `social.php`라는 이름의 설정 파일들도 존재해야 합니다.

그리고 Administrator의 헤더 네비게이션은 그대로 유지하면서 콘텐츠 영역은 개발자가 완전히 제어할 수 있는 커스텀 뷰 페이지를 지정할 수도 있습니다. 이 경우 뷰 경로 앞에 `page.`를 붙여 메뉴 배열에 전달하면 됩니다. 또한 뷰 컴포저(View Composer)를 사용하여 커스텀 페이지에 필요한 JS나 CSS 자산을 추가할 수도 있습니다:

	View::composer(array('administrator::layouts.default'), function($view)
	{
		// 먼저 이것이 커스텀 페이지인지 확인합니다
		if ($view->page === 'ecommerce.analytics')
		{
			// 페이지 전용 자산을 추가합니다
			$view->js += array(
				'highcharts' => '/path/to/highcharts.js'
			);

			$view->css += array(
				'mycss' => '/path/to/my.css'
			);
		}
	});

서브메뉴를 만들고 싶다면, 문자열 대신 문자열 배열을 값으로 전달하십시오. 이 배열 슬롯의 키(인덱스)가 UI 상에서 서브메뉴의 제목이 됩니다. 서브메뉴 내부에서 또 서브메뉴를 가질 수 있으며 깊이(Depth) 제한은 없습니다.

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/menu.png" />

> 모델 설정 옵션에 대한 자세한 설명은 **[모델 설정 문서](/docs/model-configuration)**를 참고하십시오.
> 설정(Settings) 파일 옵션에 대한 자세한 설명은 **[설정(Settings) 구성 문서](/docs/settings-configuration)**를 참고하십시오.

<a name="permission"></a>
### Permission

	/**
	 * 권한(Permission) 옵션은 가장 높은 수준의 인증 검사로, 현재 사용자가 관리자 섹션을 볼 수 있는지
	 * 여부를 확인하여 true를 반환해야 하는 클로저(Closure)를 정의합니다.
	 * false로 평가되는 모든 응답은 사용자를 아래에 정의된 'login_path'로 보냅니다.
	 *
	 * @type closure
	 */
	'permission'=> function()
	{
		return Auth::check();
	},

권한 옵션을 통해 현재 사용자가 Administrator 전체에 접근할 수 있는지 여부를 결정하는 클로저를 정의할 수 있습니다. 각 모델의 설정 파일에서도 모델별 권한을 별도로 정의할 수 있습니다. 이 클로저가 참으로 평가되는 값을 반환할 때만 사용자에게 접근 권한이 부여됩니다. 이 검사에 실패하면 사용자는 `login_path`로 리다이렉트됩니다.

<a name="use-dashboard"></a>
### Use Dashboard

	/**
	 * 대시보드를 사용할지(dashboard_view 옵션에 뷰를 제공해야 함), 아니면 대시보드가 없는 홈 페이지를
	 * 사용할지(home_page 옵션에 메뉴 항목을 제공해야 함) 결정합니다.
	 *
	 * @type bool
	 */
	'use_dashboard' => false,

Administrator가 모든 형태의 대시보드 요구사항을 다 해결할 수 있는 것은 아닙니다. 때로는 대시보드에 여러 위젯형 모듈을 배치하는 것이 나을 수도 있고, 단순히 여러 버튼들을 배치하는 게 적절할 수도 있으며, 대시보드 자체가 무의미한 경우도 있습니다. 이에 대해 두 가지 일반적인 옵션이 제공됩니다: 애플리케이션 내에 원하는 대로 대시보드 뷰를 생성하여 구성하거나, `menu` 옵션 내의 특정 위치를 공통 "홈" 페이지로 사용할 수 있습니다.

`use_dashboard`를 **true**로 설정하면, 아래의 `dashboard_view` 옵션에 정의된 값을 찾아 해당 뷰를 Administrator의 콘텐츠 영역에 로드합니다.

`use_dashboard`를 **false**로 설정하면, 아래의 `home_page` 옵션에 정의된 값을 찾아 사용자가 Administrator 홈으로 들어왔을 때 해당 페이지를 로드합니다.

두 경우 모두 값(뷰 또는 메뉴 항목 중 하나)을 찾을 수 없으면 에러가 발생합니다.

<a name="dashboard-view"></a>
### Dashboard View

	/**
	 * 대시보드 뷰를 생성하려면, 여기에 뷰 문자열을 제공하십시오.
	 *
	 * @type string
	 */
	'dashboard_view' => 'administrator.dashboard',

`use_dashboard` 옵션이 true로 설정된 경우, Administrator는 이 뷰를 Administrator 콘텐츠 영역에 로드하려고 시도합니다. 이 뷰는 원하시는 방식으로 자유롭게 구성할 수 있으며, [뷰 컴포저(View Composer)](http://laravel.com/docs/responses#view-composers)를 사용하여 필요한 모든 데이터를 이 뷰에 주입할 수 있습니다.

<a name="home-page"></a>
### Home Page

	/**
	 * 관리자 섹션의 기본 랜딩 페이지로 사용될 메뉴 항목입니다.
	 *
	 * @type string
	 */
	'home_page' => 'products',

`use_dashboard` 옵션이 false로 설정된 경우, 사용자가 Administrator 기본 URL을 방문했을 때 위의 페이지로 리다이렉트합니다. 이 값은 반드시 `menu` 옵션에 정의된 항목 중 하나와 일치해야 합니다.

<a name="back-to-site-path"></a>
### Back To Site Path

	/**
	 * 사용자가 "사이트로 돌아가기" 버튼을 클릭했을 때 이동할 라우트입니다.
	 *
	 * @type string
	 */
	'back_to_site_path' => '/',

라라벨의 `URL::to()` 메서드와 호환되는 값을 제공해 주시면 됩니다.

<a name="login-path"></a>
### Login Path

	/**
	 * 사용자가 권한 검사에 실패했을 때 Administrator가 사용자를 보낼 경로입니다.
	 *
	 * @type string
	 */
	'login_path' => 'user/login',

라라벨의 `URL::to()` 메서드와 호환되는 값을 제공해 주시면 됩니다.

<a name="logout-path"></a>
### Logout Path

	/**
	 * 로그아웃 경로는 사용자가 로그아웃 링크를 클릭했을 때 Administrator가 보낼 경로입니다.
	 *
	 * @type string
	 */
	'logout_path' => URL::route('logout'),

관리자 사용자에게 관리자 인터페이스 내에서 로그아웃할 수 있는 기능을 제공하려면 `logout_path` 문자열을 지정할 수 있습니다. 값을 입력하면 화면 오른쪽 상단에 지정한 경로로 사용자를 보내는 앵커 링크가 표시됩니다. 기본적으로 `logout_path` 옵션은 `false`로 설정되어 있습니다.

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/logout-button.png" />

<a name="redirect-key"></a>
### Redirect Key

	/**
	 * login_path로 전달되는 이전 URL의 입력 키입니다. Input::get('redirect')를 수행하여 가져올 수 있습니다.
	 *
	 * @type string
	 */
	'login_redirect_key' => 'redirect',

사용자가 `login_path`로 리다이렉트될 때 리다이렉트될 이전 경로가 함께 전송됩니다. 이 옵션을 통해 해당 키의 이름을 정의할 수 있습니다. 예를 들어 위와 같이 설정한 경우, `Session::get('redirect')`를 사용하여 리다이렉트 URL을 가져올 수 있습니다.

<a name="global-rows-per-page"></a>
### Global Rows Per Page

	/**
	 * 사용자가 모델별로 커스텀 페이지당 행 수를 지정하지 않은 경우 사용하는 폴백(fallback) 기본값입니다.
	 *
	 * @type NULL|int
	 */
	'global_rows_per_page' => 20,

관리자 사용자는 각 모델에서 다음 드롭다운을 통해 페이지당 행 수를 설정할 수 있습니다:

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/rows-per-page.png" />

이 설정은 사용자의 세션이 만료될 때까지 페이지 로드 간에 유지됩니다. `global_rows_per_page` 옵션은 사용자가 특정 모델에 대해 원하는 페이지당 개수를 설정하지 않았을 때 사용할 기본값입니다.

<a name="locales"></a>
### Locales

	/**
	 * 사용 가능한 로케일 문자열의 배열입니다. 이는 Administrator 인터페이스의 우측 상단 언어 메뉴에서
	 * 어떤 언어를 선택할 수 있는지 결정합니다.
	 *
	 * @type array
	 */
	'locales' => array('en', 'de', 'tr'),

이 로케일 문자열 배열을 제공하면, 관리자 사용자는 Administrator 인터페이스 우측 상단에 위치한 로케일 메뉴를 통해 다양한 언어를 선택할 수 있게 됩니다:

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/localization.png" />

사용자가 선택한 로케일은 세션이 만료될 때까지 페이지가 변경되어도 그대로 유지됩니다.
