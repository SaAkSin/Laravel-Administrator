# 모델 설정 (Model Configuration)

- [소개](#introduction)
- [예제](#examples)
- [옵션](#options)

<a name="introduction"></a>
## 소개 (Introduction)

모든 Eloquent 모델(또는 최종적으로 Eloquent 모델을 상속받는 모든 객체)은 모델 설정 파일로 표현될 수 있습니다. 이 파일들은 애플리케이션 디렉터리 구조 내 어디에나 위치할 수 있습니다. `app/config/packages/frozennode/administrator/administrator.php` 설정 파일의 [`model_config_path`](/docs/configuration#model-config-path) 옵션에 설정 파일들이 있는 경로만 제공해주면 됩니다. 이 설정 파일들의 **파일명**은 `administrator.php` 설정 내 [`menu`](/docs/configuration#menu) 옵션에 제공된 값들과 일치해야 합니다.

> **참고**: 이 파일명들은 관리자 인터페이스에서 각 모델의 **URI**로도 사용됩니다.

모델 설정 파일이 올바르게 작동하려면 반드시 제공해야 하는 몇 가지 필수 필드가 있습니다. 이 외에도, 모델별로 관리자 인터페이스를 커스터마이징하는 데 도움이 되는 다양한 옵션 필드를 정의할 수 있습니다. 예를 들어, 특정 모델에 WYSIWYG 필드가 필요한 경우 수정 폼의 너비를 기본값보다 더 넓게 만들고 싶을 것입니다. 이때는 해당 모델 설정에서 `form_width` 옵션을 설정해주기만 하면 됩니다.

<a name="examples"></a>
## 예제 (Examples)

예제 설정 파일을 확인하려면, [Administrator GitHub 리포지토리](https://github.com/FrozenNode/Laravel-Administrator/tree/master/examples)의 `/examples` 디렉터리를 참고하십시오.

<a name="options"></a>
## 옵션 (Options)

아래는 사용할 수 있는 모든 옵션의 목록입니다. 필수 옵션은 *(필수)*로 표시되어 있습니다.

- [Title](#title) *(필수)*
- [Single](#single) *(필수)*
- [Model](#model) *(필수)*
- [Columns](#columns) *(필수)*
- [Edit Fields](#edit-fields) *(필수)*
- [Filters](#filters)
- [Query Filter](#query-filter)
- [Permission](#permission)
- [Action Permissions](#action-permissions)
- [Custom Actions](#custom-actions)
- [Global Custom Actions](#global-custom-actions)
- [Form Request Option](#form-request-option)
- [Validation Rules](#validation-rules)
- [Validation Messages](#validation-messages)
- [Sort](#sort)
- [Form Width](#form-width)
- [Link](#link)
- [VIEW](#view)

<a name="title"></a>
### Title *(필수)*

	/**
	 * 모델 제목
	 *
	 * @type string
	 */
	'title' => 'Collection',

메뉴와 모델의 기본 제목으로 사용되는 모델의 제목입니다.

<a name="single"></a>
### Single *(필수)*

	/**
	 * 모델의 단수형 이름
	 *
	 * @type string
	 */
	'single' => 'collection',

Administrator 내에서 단수형 이름을 사용해야 하는 모든 곳에 사용됩니다. 예를 들어, 새 항목 생성을 시작하는 버튼은 이 설정을 기반으로 생성됩니다. 이 예시의 경우 버튼은 "New collection"으로 생성됩니다.

<a name="model"></a>
### Model *(필수)*

	/**
	 * 이 설정이 나타내는 Eloquent 모델의 클래스 이름
	 *
	 * @type string
	 */
	'model' => 'Collection',

반드시 Eloquent 모델의 정규화된 네임스페이스를 포함한 클래스 이름(fully-qualified class name)이어야 합니다. 이 예시에서는 `Collection` 모델을 예로 들고 있습니다. 모델에 네임스페이스를 사용하고 있다면 전체 네임스페이스 경로를 제공해야 합니다.

<a name="columns"></a>
### Columns *(필수)*

	/**
	 * 컬럼 배열
	 *
	 * @type array
	 */
	'columns' => array(
		'ordering' => array(
			'title' => 'Order'
		),
		'image' => array(
			'title' => 'Image',
			'output' => '<img src="/uploads/homepagesliders/resize/(:value)" height="100" />',
		),
		'link' => array(
			'title' => 'Link',
			'output' => '<a href="(:value)" target="_blank">(:value)</a>',
		),
		'product_name' => array(
			'title' => 'Product',
			'relationship' => 'product',
			'select' => '(:table).name',
		)
	),

결과 세트에 표시될 컬럼들입니다. 위에서 볼 수 있듯이 출력 형식을 수정하거나, 사용자 정의 SQL SELECT를 수행하거나, Eloquent 관계를 기반으로 이 필드에 대한 관계형 정보를 가져오는 등 매우 광범위하게 커스터마이징할 수 있습니다.

> 모든 컬럼 옵션에 대한 자세한 설명은 **[컬럼 문서](/docs/columns)**를 참고하십시오.

<a name="edit-fields"></a>
### Edit Fields *(필수)*

	/**
	 * 수정 필드(edit fields) 배열
	 *
	 * @type array
	 */
	'edit_fields' => array(
		'name' => array(
			'title' => 'Name',
			'type' => 'text'
		),
		'product' => array(
			'title' => 'Product',
			'type' => 'relationship'
		),
		'image' => array(
			'title' => 'Image (1200 x 1314)',
			'type' => 'image',
			'naming' => 'random',
			'location' => 'public/uploads/products/originals/',
			'size_limit' => 2,
			'sizes' => array(
		 		array(1200, 1314, 'crop', 'public/uploads/products/resize/', 100),
		 		array(452, 495, 'landscape', 'public/uploads/products/detail/', 100),
		 	)
		)
	),

`edit_fields` 배열을 사용하여 모델에 수정 가능한 필드들을 정의할 수 있습니다. 대부분의 기본 데이터 타입과 관계(relationship)와 같은 복잡한 필드를 포함하여 다양한 타입의 필드를 지원합니다. 모델 테이블 상의 실제 필드를 나타내려면 `edit_fields` 배열 항목의 키를 모델의 어트리뷰트(attribute) 이름과 일치시키면 됩니다. 표시하려는 컬럼이 사용자 정의 SELECT 컬럼이거나 관계(relationship) 컬럼인 경우에는 항목의 키가 해당 컬럼의 별칭(alias)이 됩니다.

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/edit-form.png" />

> 모든 수정 필드 타입과 옵션에 대한 자세한 설명은 **[필드 문서](/docs/fields)**를 참고하십시오.

<a name="filters"></a>
### Filters

	/**
	 * 필터 필드
	 *
	 * @type array
	 */
	'filters' => array(
		'id',
		'name' => array(
			'title' => 'Name',
		),
		'date' => array(
			'title' => 'Date',
			'type' => 'date',
		),
	),

`filters` 배열을 사용하여 모델의 검색 필터를 정의할 수 있습니다. 필터할 수 있는 필드 타입의 종류가 더 적다는 점을 제외하면 `edit_fields` 항목과 매우 유사하게 동작합니다. 예를 들어 `edit_fields` 배열에는 `image` 필드 타입을 가질 수 있지만 `filters` 배열에는 `'type' => 'image'`를 지정할 수 없습니다. 대신 이미지 필드를 `text` 필드 타입으로 지정하면 이미지 이름으로 필터링하는 등의 구현은 가능합니다.

> 모든 필터 타입과 옵션에 대한 자세한 설명은 **[필터 문서](/docs/fields#filters)**를 참고하십시오.

<a name="query-filter"></a>
## Query Filter

	/**
	 * 쿼리 필터 옵션을 사용하면 Administrator가 쿼리를 작성하기 전에 쿼리 파라미터를 수정할 수 있습니다.
	 * 예를 들어, 한 페이지에는 삭제된 항목만 보여주고 다른 페이지에는 삭제되지 않은 항목만 표시하려는 경우
	 * 쿼리 필터를 사용하여 처리할 수 있습니다.
	 *
	 * @type closure
	 */
	'query_filter'=> function($query)
	{
		if (!Auth::user()->hasRole('super_admin'))
		{
			$query->whereDeleted(false);
		}
	},

쿼리 필터 옵션을 사용하면 Administrator가 모델 결과를 가져오기 위한 쿼리를 작성하기 전에 실행될 클로저(closure)를 정의할 수 있습니다. 이 함수에는 쿼리 빌더 객체가 인자로 전달되며, 이를 이용해 현재 사용자가 볼 수 있는 레코드를 제한할 수 있습니다. 위 예시처럼 사용자 인증 시스템과 연동해 사용하거나 필요한 비즈니스 로직에 맞추어 자유롭게 활용할 수 있습니다.

> **참고:** `query_filter` 함수로 전달되는 쿼리 빌더 객체에는 필터 조건들이 아직 적용되지 않은 상태이지만, 이미 현재 테이블의 기본 키(primary key) 필드를 기준으로 그룹화(grouped)되어 있습니다.

<a name="permission"></a>
## Permission

	/**
	 * 권한(permission) 옵션은 모델별 인증 검사 기능입니다.
	 * 현재 사용자가 이 모델을 볼 수 있는 권한이 있을 경우 true를 반환하는 클로저를 정의합니다.
	 * "거짓(falsey)"을 반환하면 404 에러가 발생합니다.
	 *
	 * @type closure
	 */
	'permission'=> function()
	{
		return Auth::user()->hasRole('developer');
	},

권한 옵션을 사용하면 현재 사용자가 이 모델에 접근할 수 있는지 여부를 결정하는 클로저를 정의할 수 있습니다. 이 필드가 제공된 경우(필수 아님), 이 클로저의 연산 결과가 "참(truthy)"인 값으로 평가되어야만 사용자에게 접근 권한이 부여됩니다. 만약 거짓으로 평가되는 값을 반환하면 설정해 둔 `login_path`로 리다이렉트됩니다. 클로저에서 `Response`나 `Redirect` 객체를 직접 반환하면 해당 요청이 처리됩니다. 반환된 `Redirect` 객체의 "with" 데이터에는 로그인 리다이렉트 경로가 추가됩니다.

<a name="action-permissions"></a>
## Action Permissions

	/**
	 * action_permissions 옵션을 사용하면 네 가지 기본 액션인 'create', 'update', 'delete', 'view'에 대한 권한을 정의할 수 있습니다.
	 * 또한 커스텀 액션에 대한 권한을 정의하는 추가적인 공간으로도 사용할 수 있습니다.
	 *
	 * @type array
	 */
	'action_permissions'=> array(
		'delete' => function($model)
		{
			return Auth::user()->has_role('developer');
		}
	),

네 가지 기본 액션(`create`, `update`, `delete`, `view`) 및 직접 정의한 모든 커스텀 액션에 대한 접근 제어를 위해 액션 권한을 제공할 수 있습니다. 이 중 어떤 옵션도 필수가 아니며, 접근을 제한하려는 경우에만 사용하면 됩니다. 위의 예제에서는 `developer` 역할을 가진 사용자만 이 모델의 항목을 삭제할 수 있습니다. `action_permissions` 배열의 키는 액션 명이어야 하며, 각 항목은 true 또는 false를 반환하는 익명 함수이거나 단순히 불리언(boolean) 값이어야 합니다.

<a name="custom-actions"></a>
## Custom Actions

	/**
	 * 이곳에 모델의 커스텀 액션을 정의할 수 있습니다.
	 */
	'actions' => array(
		// 항목 순서를 위로 올림
		'order_up' => array(
			'title' => 'Order Up',
			'messages' => array(
				'active' => '순서 재정렬 중...',
				'success' => '순서 재정렬 완료',
				'error' => '순서를 재정렬하는 동안 오류가 발생했습니다',
			),
			'permission' => function($model)
			{
				return $model->category_id !== 2;
			},
			// 클로저로 모델이 전달됩니다
			'action' => function($model)
			{
				// 이 모델의 모든 항목을 가져와 순서를 재정렬합니다
				$model->orderUp();
			}
		),

		// 항목 순서를 아래로 내림
		'order_down' => array(...),
	),

관리 사용자에게 커스텀 코드를 실행할 수 있는 버튼을 제공하려는 경우 모델에 대한 커스텀 액션을 정의할 수 있습니다. 위 예시에서는 다음과 같은 두 개의 버튼이 생성됩니다.

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/custom-actions.png" />

사용자가 어느 한 버튼을 클릭하면, 정의된 `action` 프로퍼티(콜백 함수)가 실행되며 관련 Eloquent 모델 객체가 인자로 전달됩니다.

> 커스텀 액션에 대한 자세한 설명은 **[액션 문서](/docs/actions)**를 참고하십시오.

<a name="global-custom-actions"></a>
### Global Custom Actions

	/**
	 * 이곳에 모델의 글로벌 커스텀 액션을 정의할 수 있습니다.
	 */
	'global_actions' => array(
		// 엑셀 다운로드 생성
		'download_excel' => array(
			'title' => 'Download XLS',
			'messages' => array(
				'active' => '스프레드시트를 생성하는 중...',
				'success' => '스프레드시트가 생성되었습니다! 다운로드를 시작합니다...',
				'error' => '스프레드시트를 생성하는 동안 오류가 발생했습니다',
			),
			// 클로저로 Eloquent 쿼리 빌더가 전달됩니다
			'action' => function($query)
			{
				// 이 쿼리의 모든 로우(row)를 가져옵니다
				$result = $query->get();

				// 데이터를 엑셀 형식으로 가공하는 작업 수행

				// 다운로드 응답 반환
				return Response::download($filePath);
			}
		),
	),

글로벌 커스텀 액션은 모델 페이지에서 언제든지 클릭할 수 있는 버튼입니다. 대부분의 경우 일반 커스텀 액션과 매우 유사하게 동작합니다. 다만, `action` 콜백 함수에 단일 모델 대신 모든 검색 필터 조건이 이미 적용된(limit 및 offset 제외) 쿼리 빌더 객체가 전달된다는 차이가 있습니다.

> 커스텀 액션에 대한 자세한 설명은 **[액션 문서](/docs/actions)**를 참고하십시오.

<a name="form-request-option"></a>
### Form Request Option

	/**
	 * 폼의 유효성 검사에 사용할 Laravel의 폼 리퀘스트(Form Request) 기반 클래스
	 *
	 * @type string
	 */
    'form_request' => 'FormRequestPath',

`form_request` 옵션을 사용하면 저장 시 수정된 필드들의 유효성을 검사할 커스텀 [폼 리퀘스트(Form Request)](http://laravel.com/docs/validation#form-request-validation) 클래스를 정의할 수 있습니다. 해당 폼 리퀘스트 객체에는 유효성 검사를 수행할 rules 배열이 포함되어 있어야 합니다. 이 옵션은 기본적으로 선택 사항(optional)입니다.

<a name="validation-rules"></a>
### Validation Rules

	/**
	 * Laravel 유효성 검사 클래스를 기반으로 하는 폼의 유효성 검사 규칙
	 *
	 * @type array
	 */
	'rules' => array(
		'name' => 'required',
		'age' => 'required|integer|min:18',
	),

`rules` 옵션을 사용하여 모델의 유효성 검사 규칙을 지정할 수 있습니다. Administrator는 모델의 유효성을 검사하기 위해 [Laravel의 유효성 검사(validation)](http://laravel.com/docs/validation) 기능을 활용합니다. 폼 입력값이 유효하지 않은 경우, 데이터를 저장하지 않고 관리 사용자에게 이를 알립니다.

> 유효성 검사에 대한 자세한 설명은 **[유효성 검사 문서](/docs/validation)**를 참고하십시오.

<a name="validation-messages"></a>
### Validation Messages

	/**
	 * Laravel 유효성 검사 클래스를 기반으로 하는 폼의 유효성 검사 메시지
	 *
	 * @type array
	 */
	'messages' => array(
		'name.required' => '이름 필드는 필수 입력 항목입니다',
		'age.min' => '최소 나이는 18세입니다',
	),

`messages` 옵션을 사용하여 모델의 유효성 검사 실패 시 보여줄 오류 메시지를 설정할 수 있습니다. Administrator는 [Laravel의 유효성 검사(validation)](http://laravel.com/docs/validation#custom-error-messages) 기능을 사용하며, 메시지는 Laravel의 사용자 정의 오류 메시지와 동일한 포맷을 따라야 합니다.

> 유효성 검사에 대한 자세한 설명은 **[유효성 검사 문서](/docs/validation)**를 참고하십시오.

<a name="sort"></a>
## Sort

	/**
	 * 모델의 정렬 옵션
	 *
	 * @type array
	 */
	'sort' => array(
		'field' => 'name',
		'direction' => 'asc',
	),

`sort` 옵션은 두 개의 키(`field`와 `direction`)를 가지는 배열이어야 합니다. `field`는 반드시 `columns` 배열에 정의되어 있는 컬럼명 중 하나여야 하며, `direction`은 `asc` 또는 `desc`여야 합니다.

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/sorting.png" />

<a name="form-width"></a>
## Form Width

	/**
	 * 모델 수정 폼의 너비
	 *
	 * @type int
	 */
	'form_width' => 400,

이 값을 285보다 큰 정수값으로 설정하면 수정 필드의 너비가 확장되고 컬럼 목록의 너비는 그에 맞게 줄어듭니다. `true`로 설정하면 기본값으로 500이 적용됩니다. 값을 적절히 조절하면서 모델에 가장 잘 어울리는 크기를 찾아보시기 바랍니다.

<a name="link"></a>
## Link

	/**
	 * 이 옵션이 설정되면 모델의 프론트엔드 링크를 생성하기 위해 실행됩니다.
	 *
	 * @type function
	 */
	'link' => function($model)
	{
		return URL::route('product', array($model->collection()->first()->uri, $model->uri));
	},

모델에 매핑되는 프론트엔드 링크가 존재하는 경우, 수정 폼 최상단에 해당 페이지로 이동할 수 있는 "항목 보기(view item)" 링크를 표시하고 싶을 수 있습니다. 관련 `$model` 객체가 인자로 전달되므로 이를 사용해 URL을 완성할 수 있으며, 클로저는 유효한 URL 문자열을 반환해야 합니다.

<a name="view"></a>
## VIEW

    'view' => true

VIEW 모델인 경우 조회 시 성능 개선이 있습니다.
