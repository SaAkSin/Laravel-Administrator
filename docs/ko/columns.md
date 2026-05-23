# 컬럼 (Columns)

- [소개](#introduction)
- [단순 컬럼](#simple-columns)
- [컬럼 헤더](#column-headers)
- [표시 옵션 (Visible Option)](#visible-option)
- [접근자 (Accessors) 사용하기](#using-accessors)
- [정렬 필드 설정하기](#setting-the-sort-field)
- [커스텀 셀렉트 (Custom Selects)](#custom-selects)
- [관계 컬럼 (Relationship Columns)](#relationship-columns)
- [커스텀 출력 (Custom Outputs)](#custom-outputs)

<a name="introduction"></a>
## 소개 (Introduction)

각 모델의 설정(config) 파일에는 반드시 `columns` 옵션을 지정해야 합니다. 이 옵션은 모델의 결과 테이블에 표시하고자 하는 컬럼들의 배열이어야 합니다. 컬럼은 다음과 같은 요소들로 구성할 수 있습니다:

* 모델의 속성(Attribute) 이름
* [접근자 (Accessors)](http://laravel.com/docs/eloquent#accessors-and-mutators)
* [관계 (Relationships)](#relationship-columns) ([중첩된 관계 (nested relationships)](/docs/relationship-columns#nested-relationships) 포함)

`title` 옵션을 통해 컬럼의 헤더를 제어할 수 있으며, 해당 컬럼을 기준으로 정렬할 때 사용될 `sort_field` 및 해당 컬럼에 대해 원하는 커스텀 출력을 설정할 수 있습니다.


<a name="simple-columns"></a>
## 단순 컬럼 (Simple Columns)

데이터베이스 테이블의 값을 그대로 출력하려면 다음과 같이 작성하면 됩니다:

	'columns' => array(
		'id',
		'name',
		'price',
	)

각 값은 데이터베이스 테이블의 컬럼 이름과 일치해야 합니다. 다음 몇 개의 섹션에서 다룰 내용처럼 더 복잡하게 구성하는 경우에는, 다음과 같이 컬럼 이름을 배열 항목의 *키(key)*로 설정해야 합니다:

	'columns' => array(
		'id' => array(
			'title' => 'ID'
		),
	)

다른 테이블이나 커스텀 셀렉트(custom select)로부터 이 컬럼을 도출해내는 경우, 이 키는 Administrator에서 컬럼의 별칭(alias)으로 사용됩니다.

> 본 페이지의 나머지 부분에서는 예제에서 `'columns' => array()` 부분을 생략합니다. 명백하지 않은 경우를 제외하고, 이후의 모든 예제는 `columns` 배열 내부에 위치한다고 가정합니다.

<a name="column-headers"></a>
## 컬럼 헤더 (Column Headers)

컬럼 헤더의 출력을 제어하려면 `title` 옵션을 설정하십시오:

	'id' => array(
		'title' => 'ID'
	)

<a name="visible-option"></a>
## 표시 옵션 (Visible Option)

`visible` 옵션을 사용하면 컬럼의 표시 여부를 결정할 수 있습니다. 기본값은 불리언 `true`입니다. 불리언 `false`를 전달하면 컬럼이 숨겨집니다. 또한 현재 데이터 모델을 단일 매개변수로 받는 클로저(Closure)를 전달할 수도 있습니다. 해당 사용자에게 컬럼을 표시하려면 참(truthy) 값을 반환하고, 숨기려면 거짓(falsey) 값을 반환하면 됩니다.

	'secret_info' => array(
		'title' => 'Secret Info',
		'visible' => function($model)
		{
			return Auth::user()->hasRole('super_admin');
		},
	),

<a name="using-accessors"></a>
## 접근자 (Accessors) 사용하기

[Eloquent 접근자 (Accessors)](http://laravel.com/docs/eloquent#accessors-and-mutators) 또한 컬럼 값으로 사용할 수 있습니다. 예를 들어, `salary`라는 컬럼이 있고 Eloquent 모델에 다음과 같은 접근자가 정의되어 있다고 가정해 보겠습니다:

	public function getFormattedSalaryAttribute()
	{
		return '$'.number_format($this->getAttribute('salary'), 2);
	}

그러면 다음과 같이 `formatted_salary`를 컬럼의 키로 참조할 수 있습니다:

	'formatted_salary' => array(
		'title' => 'Formatted Salary'
	)

> **주의**: `sort_field`를 정의하기 전까지는 접근자 컬럼을 정렬할 수 없습니다!

<a name="setting-the-sort-field"></a>
## 정렬 필드 설정하기 (Setting the Sort Field)

접근자를 사용하는 경우, Administrator가 정렬에 사용할 수 있도록 `sort_field`를 정의하고 싶을 수 있습니다. 접근자는 모델의 데이터베이스 테이블에 실제로 존재하지 않기 때문에 이 설정이 필요합니다. 다음과 같이 해당 테이블의 필드를 참조하기만 하면 됩니다:

	'formatted_salary' => array(
		'title' => 'Formatted Salary',
		'sort_field' => 'salary',
	)

<a name="unsortable-columns"></a>
## 정렬 불가능한 컬럼 (Unsortable Columns)

특정 컬럼의 정렬 기능을 비활성화하려면 `sortable` 옵션을 `false`로 설정할 수 있습니다:

	'image' => array(
		'title' => 'Image',
		'output' => '<img src="/uploads/products/resize/(:value)" height="100" />',
		'sortable' => false,
	)

<a name="custom-selects"></a>
## 커스텀 셀렉트 (Custom Selects)

모델의 표준 컬럼이나 접근자를 사용하는 것으로 만족하지 못하는 경우, 컬럼을 커스텀 `select` 문으로 생성할 수도 있습니다. 여기서는 유효한 모든 SQL SELECT 문을 사용할 수 있습니다. 이는 SELECT 함수 등을 활용하고 싶을 때 유용합니다. 또한 모든 작업이 SQL 내에서 처리되기 때문에, 접근자를 사용하는 것보다 성능 면에서 (아주) 미세한 이점이 있습니다.

커스텀 `select`를 정의할 때는 테이블 내의 임의의 컬럼 앞에 반드시 `(:table).` 접두사를 붙여야 합니다. 모델의 결과 집합을 가져오기 위해 수행되는 쿼리가 대개 여러 다른 테이블들을 조인하기 때문에 이 접두사가 필수적입니다. 커스텀 `select` 옵션은 다음과 같은 형태가 됩니다:

	'good' => array(
		'title' => 'Is Good',
		'select' => "IF((:table).is_good, 'yes', 'no')",
	)

여기서 `good` 키는 컬럼의 별칭(alias)이 되므로 원하는 이름으로 자유롭게 지정할 수 있습니다. 특정 행의 `is_good` 값이 1이면 'yes'로 표시되고, 0이면 'no'로 표시됩니다.

<a name="relationship-columns"></a>
## 관계 컬럼 (Relationship Columns)

> 관계 컬럼에 대해 더 자세히 알아보려면 [관계 컬럼 문서 (relationship columns docs)](/docs/relationship-columns)를 확인해 주십시오.

적당히 복잡한 데이터베이스 구조에서는 테이블에 다른 테이블의 ID를 나타내는 컬럼이 있을 수 있습니다. 대부분의 경우 관리자 사용자에게 이러한 ID를 직접 보여주는 것은 무의미합니다. 컴퓨터에게는 숫자 ID가 유용할지 몰라도 사람에게는 그렇지 않기 때문입니다. 혹은 모델의 테이블에 관계가 전혀 표현되어 있지 않고, 대신 두 테이블을 연결하는 피벗(pivot) 테이블이나 다른 모델의 테이블 내 컬럼으로 존재할 수도 있습니다.

연관된 컬럼을 표시하고 싶다면 `relationship` 옵션을 제공하면 됩니다. 이 옵션의 값은 반드시 *모델에 정의된 Eloquent 관계(relationship) 메서드의 이름*이어야 합니다. 이와 함께 Administrator가 관계 테이블로부터 값을 가져오는 데 사용할 `select` 옵션도 제공해야 합니다. 예를 들어, `Director` 모델이 있고 감독이 참여한 영화의 수를 세고 싶다면 다음과 같이 작성할 수 있습니다:

	'num_films' => array(
		'title' => '# Films',
		'relationship' => 'films', // 이것은 Eloquent 관계 메서드의 이름입니다!
		'select' => "COUNT((:table).id)",
	)

`select` 문에서는 임의의 SQL 그룹화 함수를 모두 사용할 수 있습니다. `relationship` 옵션을 사용하면 이외에도 훨씬 많은 것들을 처리할 수 있으므로, 더 자세한 내용은 [관계 컬럼 문서 (relationship columns docs)](/docs/relationship-columns)를 참고하시기 바랍니다.

<a name="custom-outputs"></a>
## 커스텀 출력 (Custom Outputs)

컬럼에 단순한 텍스트 이상의 것을 보여주고 싶다면 `output` 옵션을 사용하면 됩니다. 이 옵션값은 문자열이거나 익명 함수(anonymous function)일 수 있습니다.

익명 함수를 제공하는 경우, 사용할 수 있는 인수(arguments)는 데이터베이스로부터 가져온 해당 컬럼의 값과 현재 모델 객체입니다. 예를 들어, [컬러 (color)](/docs/field-type-color) 필드를 사용 중이고 관리자 사용자에게 해당 행의 색상이 무엇인지 명확하게 보여주고 싶다면 다음과 같이 작성할 수 있습니다:

	'hex' => array(
		'title' => 'Color',
		'output' => function($value, $model)
		{
			return '<div style="background-color: ' . $value . '; width: 200px; height: 20px; border-radius: 2px;"></div>';
		},
	),

또는 `output` 옵션에 문자열을 전달할 수도 있으며, 이 경우 Administrator는 해당 문자열 안의 `(:value)` 부분을 이 컬럼의 행 값으로 교체합니다.

	'hex' => array(
		'title' => 'Color',
		'output' => '<div style="background-color: (:value); width: 200px; height: 20px; border-radius: 2px;"></div>',
	),

또는 [이미지 (image)](/docs/field-type-image) 필드를 사용 중이고 결과 집합에 이미지를 표시하고 싶은 경우도 있을 것입니다. 이 경우에는 다음과 같이 작성합니다:

	'banner_image' => array(
		'title' => 'Banner Image',
		'output' => '<img src="/uploads/collections/resize/(:value)" height="100" />',
	),

Custom outputs are available for all column types, not just columns that are on the model's table.
