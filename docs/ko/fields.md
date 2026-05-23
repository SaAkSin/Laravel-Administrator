# 필드 (Fields)

- [소개](#introduction)
- [Title 옵션](#title-option)
- [Type 옵션](#type-option)
- [Editable 옵션](#editable-option)
- [Setter 옵션](#setter-option)
- [Visible 옵션](#visible-option)
- [Value 옵션](#value-option)
- [Description 옵션](#description-option)
- [필터 (Filters)](#filters)
- [설정 페이지 (Settings Page)](#settings-page)

<a name="introduction"></a>
## 소개

[모델 설정](/docs/model-configuration)이나 [설정 파일 구성](/docs/settings-configuration)을 진행할 때, `edit_fields` 옵션을 반드시 지정해야 합니다. 이 옵션은 모델 또는 설정 수정 폼에서 관리자 사용자에게 보여줄 필드 목록을 나타내는 배열입니다. 모델의 경우 각 필드는 모델의 SQL 컬럼 중 하나이거나 [Eloquent 관계(Eloquent relationships)](/docs/field-type-relationship) 중 하나여야 합니다. 필드가 제공되는 순서대로 관리자 사용자에게 표시됩니다.

	/**
	 * 수정 가능한 필드 목록
	 *
	 * @type array
	 */
	'edit_fields' => array(
		'surname', // 문자열만 전달된 경우, 타입은 'text'로, 타이틀은 'surname'으로 가정합니다.
		'name' => array(
			'title' => 'Name',
		),
		'published' => array(
			'title' => 'Published',
			'type' => 'bool',
		),
		'expired' => array(
			'title' => 'Expired',
			'type' => 'bool',
			'visible' => function($model)
			{
				return $model->exists;
			}
		),
		'collection' => array(
			'type' => 'relationship',
			'title' => 'Collection',
			'name_field' => 'name',
		),
		'uri' => array(
			'title' => 'URI',
			'description' => 'Uniform Resource Identifier (자동 생성을 원하시면 비워두세요)'
		),
		'image' => array(
			'title' => 'Image (1423 x 441)',
			'type' => 'image',
			'naming' => 'random',
			'location' => 'public/uploads/products/originals/',
			'size_limit' => 2,
			'sizes' => array(
		 		array(1423, 441, 'crop', 'public/uploads/products/resize/', 100),
		 	)
		)
	),

 위의 첫 번째 필드 `surname`처럼 단순한 문자열을 제공하면, 기본적으로 타이틀이 `surname`인 `text` 필드로 생성됩니다. 또한, 속성 이름이나 관계 메서드 이름과 동일한 인덱스를 가진 배열을 전달하여 더 다양한 옵션을 지정할 수도 있습니다. 여러 가지 필드 타입이 존재하며(왼쪽 메뉴에서 확인하실 수 있습니다), 각 타입별로 고유한 전용 옵션들이 있습니다.

 모든 수정 필드와 필터에 공통적으로 적용되는 범용 옵션은 `title`과 `type` 두 가지뿐입니다. 수정 필드에만 적용되고 필터에는 적용되지 않는 불리언(Boolean) 옵션으로 `editable`이 하나 더 있습니다.

 > 그 외의 다른 모든 옵션은 특정 필드 타입에만 적용됩니다. 다른 옵션들에 대한 자세한 내용은 왼쪽 메뉴에서 각 필드 타입을 확인해 주시기 바랍니다.

<a name="title-option"></a>
## Title 옵션

`title` 옵션을 사용하면 필드의 라벨을 설정할 수 있습니다.

	'name' => array(
		'title' => 'Name',
	),

<a name="type-option"></a>
## Type 옵션

`type` 옵션을 사용하면 필드의 타입을 설정할 수 있습니다. 전체 목록은 왼쪽의 Field Types 메뉴를 참조해 주십시오.

	'hex' => array(
		'title' => 'Color',
		'type' => 'color',
	),

<a name="editable-option"></a>
## Editable 옵션

`editable` 옵션은 필드를 수정할 수 있는지 여부를 결정합니다. 기본값은 `true`입니다. 관리자 사용자에게 필드를 보여주되 수정은 할 수 없도록 하려면 이 값을 `false`로 설정하십시오.

	'unique_hash' => array(
		'title' => 'Unique Hash',
		'editable' => false,
	),

`editable` 옵션에 클로저(Closure)를 전달할 수도 있습니다. 클로저의 유일한 매개변수는 Eloquent 모델(모델 페이지인 경우) 또는 설정 데이터(설정 페이지인 경우)입니다:

	'unique_hash' => array(
		'title' => 'Unique Hash',
		'editable' => function($model)
		{
			return !$model->exists; // 항목이 처음으로 저장되기 전에만 수정 가능하도록 설정합니다.
		},
	),

<a name="setter-option"></a>
## Setter 옵션

`setter` 옵션을 사용하면 Eloquent 모델에 설정은 되지만, 모델이 저장되기 전에 해제(unset)되는 속성으로 필드를 정의할 수 있습니다. 이를 통해 해당 값이 데이터베이스에 저장되는 것을 걱정하지 않고 [Mutator](http://laravel.com/docs/eloquent#accessors-and-mutators)로 사용할 수 있습니다. 기본적으로 이 옵션은 [`password`](/docs/field-type-password) 필드를 제외한 모든 필드에서 `false`로 설정되어 있습니다.

	'name' => array(
		'title' => 'Name',
		'setter' => true,
	),
	
<a name="visible-option"></a>
## Visible 옵션

`visible` 옵션은 특정 모델 상태에 따라 필드를 표시할지 여부를 결정할 수 있게 해줍니다. 이 필드의 기본값은 불리언 `true`입니다. 불리언 `false`를 전달하면 필드가 숨겨집니다. 또한 관련 `$model`을 유일한 매개변수로 받는 익명 함수를 전달할 수도 있습니다. 해당 항목에 대해 필드를 표시하려면 참(truthy) 값을 반환하고, 숨기려면 거짓(falsey) 값을 반환하면 됩니다. 이는 항목을 생성할 때나 수정할 때 특정 필드를 숨기는 데 특히 유용합니다.

	'initial_thoughts' => array(
		'title' => 'Initial Thoughts',
		'type' => 'textarea',
		'visible' => function($model)
		{
			return !$model->exists; // 항목이 처음으로 저장되기 전에만 표시됩니다.
		},
	),

<a name="value-option"></a>
## Value 옵션

`value` 옵션을 사용하면 필드의 기본값을 정의할 수 있습니다. 필터 세트의 경우 페이지가 로드될 때 적용되는 기본값이 되며, 수정 필드의 경우 새 항목을 생성할 때 사용되는 기본값이 됩니다.

	'stuff' => array(
		'title' => 'Stuff',
		'type' => 'text',
		'value' => 'foo'
	),

<a name="description-option"></a>
## Description 옵션

`description` 옵션을 사용하면 필드 라벨만으로는 사용자에게 필드와 상호작용하는 방법을 설명하기에 부족할 때 필드에 대한 추가 정보를 제공할 수 있습니다.

	'fantasy_name' => array(
		'title' => 'Fantasy Name',
		'description' => '창의적이고 독창적인 아이디어를 생각해 보세요!'
	),

<a name="filters"></a>
## 필터 (Filters)

[모델 설정](/docs/model-configuration)의 `filters` 옵션을 사용하면 특정 필드 타입을 모델 결과 집합의 필터로 사용할 수 있습니다. 필터링이 가능한 필드 타입은 [`key`](/docs/field-type-key), [`text`](/docs/field-type-text), [`number`](/docs/field-type-number), [`bool`](/docs/field-type-bool), [`enum`](/docs/field-type-enum), [`date`](/docs/field-type-date), [`time`](/docs/field-type-time), [`datetime`](/docs/field-type-datetime), 그리고 [`relationship`](/docs/field-type-relationship)입니다. 각 필드 타입의 필터는 조금씩 다르게 동작합니다. 각 필드 타입별 필터 동작 방식에 대한 자세한 내용은 왼쪽 메뉴에 있는 각 필드 타입의 문서 페이지를 참조하십시오.

	/**
	 * 필터링 가능한 필드 목록
	 *
	 * @type array
	 */
	'filters' => array(
		'name' => array(
			'title' => 'Name',
		),
		'collection' => array(
			'type' => 'relationship',
			'title' => 'Collection',
			'name_field' => 'name',
		),
		'price' => array(
			'type' => 'number',
			'title' => 'Price',
			'description' => '세금 제외 가격',
			'symbol' => '$',
			'decimals' => 2,
		),
		'colors' => array(
			'type' => 'relationship',
			'title' => 'Colors',
			'name_field' => 'name',
		),
	),

모든 필드와 마찬가지로, 배열 대신 단순한 문자열을 제공하는 경우 기본 동작은 속성 이름과 동일한 타이틀을 가진 `text` 필드로 만드는 것입니다. 속성 이름 또는 관계 메서드 이름과 동일한 인덱스를 가진 옵션 배열을 제공하면 인터페이스를 더 자세하게 제어할 수 있습니다.

필터 배열에 `value` 옵션을 제공하여 필터의 기본값을 설정할 수 있습니다. 최소값 및 최대값 입력 필드가 있는 필터의 경우 `min_value` 및 `max_value` 옵션을 설정할 수 있습니다.

	'filters' => array(
		'name' => array(
			'title' => 'Name',
			'value' => 'John',
		),
		'collection' => array(
			'type' => 'relationship',
			'title' => 'Collection',
			'name_field' => 'name',
			'value' => 13, // 선택된 항목의 ID
		),
		'price' => array(
			'type' => 'number',
			'title' => 'Price',
			'description' => '세금 제외 가격',
			'symbol' => '$',
			'decimals' => 2,
			'min_value' => 19.00,
			'max_value' => 255.45,
		),
		'colors' => array(
			'type' => 'relationship',
			'title' => 'Colors',
			'name_field' => 'name',
			'value' => array(3, 4), // ID 배열
		),
	),

<a name="settings-page"></a>
## 설정 페이지 (Settings Page)

설정 페이지를 생성할 때는 [`key`](/docs/field-type-key)와 [`relationship`](/docs/field-type-relationship)을 제외한 모든 필드 타입을 사용할 수 있습니다.

> 설정 페이지 옵션에 대한 자세한 설명은 **[설정 파일 구성 문서(settings configuration docs)](/docs/settings-configuration)**를 참조하십시오.
