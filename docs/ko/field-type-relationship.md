# 필드 타입 - 관계 (Relationship)

- [개요](#overview)
- [Belongs To](#belongs-to)
- [Belongs To 필터](#belongs-to-filter)
- [Belongs To Many](#belongs-to-many)
- [Belongs To Many 필터](#belongs-to-many-filter)
- [대용량 데이터셋과 자동 완성](#large-datasets-and-autocomplete)
- [관계 옵션 필터링](#filtering-relationship-options)
- [관계 제약 조건](#constraining-relationships)

<a name="overview"></a>
## 개요

관계(Relationship) 필드 타입은 모델의 `belongsTo` 및 `belongsToMany` 관계를 관리할 수 있게 해줍니다. 일반 필드 타입과 달리, 관계 필드 타입의 *키(Key)*는 *관계 메서드의 이름*이어야 합니다.

<a name="belongs-to"></a>
## Belongs To

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-relation-single.png" />

	'user' => array(
		'type' => 'relationship',
		'title' => 'User',
		'name_field' => 'name', // 이 객체를 나타내는 데 사용할 상대 테이블의 컬럼 또는 접근자(accessor)
	)

`name_field` 옵션을 사용하면 관계를 나타낼 상대 테이블의 컬럼 또는 접근자(accessor)를 정의할 수 있습니다. 이 필드는 다음과 같은 모델에서 사용할 수 있습니다.

	class Hat extends Eloquent {

		public function user()
		{
			return $this->belongsTo('User');
		}
	}

<a name="belongs-to-filter"></a>
## Belongs To 필터

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-relation-single-filter.png" />

`belongsTo` 필터를 사용하면 선택한 항목과 관련된 항목들로 결과 세트를 필터링할 수 있습니다.

<a name="belongs-to-many"></a>
## Belongs To Many

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-relation-multi.png" />

	'actors' => array(
		'type' => 'relationship',
		'title' => 'Actors',
		'name_field' => 'full_name', // getNameAttribute 접근자(accessor)를 사용합니다.
		'options_sort_field' => "CONCAT(first_name, ' ' , last_name)",
	)

이 경우 제공된 `name_field`는 `first_name` 필드와 `last_name` 필드를 결합하는 `User` 모델의 접근자(accessor)입니다. 하지만 `name_field`가 데이터베이스의 컬럼이 아니라 접근자이기 때문에, 옵션을 정렬하려면 `options_sort_field`도 지정해야 합니다. `options_sort_field`가 필수는 아니지만, 지정하지 않으면 기본 키 컬럼을 기준으로 오름차순 정렬됩니다. 정렬 방향은 `options_sort_direction`을 `asc` 또는 `desc`로 설정하여 지정할 수도 있습니다.

이 필드는 다음과 같은 모델에서 사용할 수 있습니다.

	class Film extends Eloquent {

		public function actors()
		{
			return $this->belongsToMany('Actor', 'films_actors');
		}
	}

이렇게 설정하면 사용자에게 영화에 출연한 모든 배우를 선택할 수 있는 다중 선택(multi-select) 필드가 표시됩니다.

만약 관리자 사용자가 선택된 값들의 순서를 변경할 수 있도록 하려면, 피벗(pivot) 테이블에 정수형 정렬 컬럼을 생성한 다음 해당 컬럼을 필드 옵션으로 지정하면 됩니다. 위의 예제에서 UI에서 드래그 앤 드롭으로 배우의 순서를 자유롭게 재정렬하고 싶다고 가정해 봅시다. 이를 위해 `films_actors` 테이블에 정수형 필드(예: `ordering`)를 추가해야 합니다. 그런 다음 모델 설정에서 `sort_field` 옵션에 해당 컬럼 이름을 입력합니다.

	'actors' => array(
		'type' => 'relationship',
		'title' => 'Actors',
		'name_field' => 'full_name', 	// getNameAttribute 접근자(accessor)를 사용합니다.
		'sort_field' => 'ordering', 	// films_actors.ordering에 있는 숫자형 컬럼을 찾습니다.
	)

이제 다중 선택 상자의 각 항목을 드래그 앤 드롭으로 정렬할 수 있습니다.

<a name="belongs-to-many-filter"></a>
## Belongs To Many 필터

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-relation-multi-filter.png" />

`belongsToMany` 필터를 사용하면 선택한 항목과 관련된 항목들로 결과 세트를 필터링할 수 있습니다. 이 필터는 점진적 배제 필터가 아니라 포함(inclusive) 필터입니다.

<a name="large-datasets-and-autocomplete"></a>
## 대용량 데이터셋과 자동 완성

관계 필드가 가리키는 다른 모델의 데이터셋이 잠재적으로 매우 큰 경우, 모든 옵션을 한 번에 로드하는 것은 원치 않을 수 있습니다. 다행히 이럴 때 `autocomplete` 옵션을 설정할 수 있습니다.

	'actors' => array(
		'type' => 'relationship',
		'title' => 'Actors',
		'name_field' => 'full_name',
		'autocomplete' => true,
		'num_options' => 5, // 기본값은 10입니다.
		'search_fields' => array("CONCAT(first_name, ' ', last_name)"), // 기본값은 array([name_field])입니다.
	)

`autocomplete`가 `true`로 설정된 관계 필드는 사용자가 값을 입력할 때까지 대기합니다.

값이 입력되면 `num_options` option에 따라 매 검색 시 사용자에게 반환할 결과의 개수가 결정됩니다.

`search_fields` 옵션은 `LIKE` 연산자로 검색할 수 있는 유효한 SQL SELECT 필드의 배열이어야 합니다. 위의 예제에서 관리자 사용자는 "Liam N"으로 검색하여 "Liam Neeson"이라는 결과를 얻을 수 있습니다. 이 필드의 기본값은 모든 관계 필드에서 제공하는 `name_field`입니다.

<a name="filtering-relationship-options"></a>
## 관계 옵션 필터링

특정 상황에서는 관계에 사용할 수 있는 옵션을 제한하고 싶을 수 있습니다. `options_filter` 옵션을 사용하면 이를 쉽게 구현할 수 있습니다.

	'actors' => array(
		'type' => 'relationship',
		'title' => 'Actors',
		'name_field' => 'full_name',
		'options_filter' => function($query)
		{
			$query->whereNull('died_at'); // 살아있는 배우만 반환합니다.
		},
	)

`options_filter`에는 쿼리 빌더 인스턴스가 전달되므로 원하는 대로 쿼리를 수정할 수 있습니다.

<a name="constraining-relationships"></a>
## 관계 제약 조건

경우에 따라 모델에 두 개 이상의 관계 필드가 존재할 수 있습니다. 이 두 필드 자체가 `hasOne`, `hasMany` 또는 `belongsToMany` 관계로 연결되어 있을 수 있습니다. 이런 상황에서는 한 필드에서 선택한 옵션에 따라 다른 필드의 옵션을 제한하는 것이 유용할 때가 있습니다. 이해를 돕기 위해 예를 들어보겠습니다.

### Has One 또는 Has Many

`Theater` 모델이 있고 관리자가 극장이 위치한 국가(Country)와 주(State)를 선택할 수 있게 하려고 한다고 가정해 봅시다. 또한 `Country` 모델과 `State` 모델도 있습니다. 주는 국가에 속하며(`belongsTo`), 국가는 여러 개의 주를 가집니다(`hasMany`). `Theater` 모델은 `Country`와 `State` 모델 모두에 속해 있습니다. 사용자가 특정 국가를 선택하면, 선택할 수 있는 주는 해당 국가에 속한 주들로 제한되어야 합니다.

따라서 `Theater` 모델은 다음과 같이 정의됩니다.

	class Theater extends Eloquent {

		public function country()
		{
			return $this->belongsTo('Country');
		}

		public function state()
		{
			return $this->belongsTo('State');
		}
	}


`Country` 모델은 다음과 같습니다.

	class Country extends Eloquent {

		public function states()
		{
			return $this->hasMany('State');
		}
	}

그리고 `State` 모델은 다음과 같습니다.

	class State extends Eloquent {

		public function country()
		{
			return $this->belongsTo('Country');
		}
	}

이제 `Theater` [모델 설정](/docs/model-configuration)을 작성할 때, [수정 필드(edit fields)](/docs/fields)를 다음과 같이 구성합니다.

	'edit_fields' => array
	(
		'country' => array(
			'title' => 'Country',
			'type' => 'relationship',
			'name_field' => 'name',
		),
		'state' => array(
			'title' => 'State',
			'type' => 'relationship',
			'name_field' => 'name',
			'constraints' => array('country' => 'states') // 이 부분이 중요합니다!
		),
	)

`state` 필드에 설정한 제약 조건(constraint)은 `Theater` 모델에서의 관계 이름(즉, 다른 필드의 이름)을 키(key)로 취하고, `Country` 모델에 정의된 `states` 관계 메서드 이름을 값(value)으로 취합니다. 이제 사용자가 국가를 선택하면 해당 국가에 속한 주들만 자동으로 선택 가능하도록 제한됩니다.

### Belongs To Many

`belongsToMany` 관계로 연결된 두 필드에 대해서도 동일한 방식을 적용할 수 있습니다. `Film` 모델과 `Theater` 모델이라는 두 개의 모델이 있다고 가정해 봅시다. 하나의 극장에서는 여러 편의 영화를 상영할 수 있고, 각 영화는 여러 극장에서 상영될 수 있습니다. 이는 표준적인 `belongsToMany` 관계입니다. `Film` 모델은 다음과 같습니다.

	class Film extends Eloquent {

		public function theaters()
		{
			return $this->belongsToMany('Theater', 'films_theaters');
		}
	}

그리고 `Theater` 모델은 다음과 같습니다.

	class Theater extends Eloquent {

		public function films()
		{
			return $this->belongsToMany('Film', 'films_theaters');
		}
	}

여기에 더해 각 극장별 영화 예매 매출을 집계하는 또 다른 모델이 있다고 상상해 봅시다. 다음과 같은 형태가 될 것입니다.

	class BoxOffice extends Eloquent {

		public function theater()
		{
			return $this->belongsTo('Theater');
		}

		public function film()
		{
			return $this->belongsTo('Film');
		}
	}

이제 `BoxOffice` [모델 설정](/docs/model-configuration)을 작성할 때, [수정 필드(edit fields)](/docs/fields)를 다음과 같이 구성할 수 있습니다.

	'edit_fields' => array
	(
		'revenue' => array(
			'title' => 'Revenue',
			'type' => 'number',
			'symbol' => '$',
			'decimals' => 2,
		),
		'film' => array(
			'title' => 'Film',
			'type' => 'relationship',
			'name_field' => 'name',
			'constraints' => array('theater' => 'films') // films는 Theater 모델의 관계 메서드 이름과 일치합니다.
		),
		'theater' => array(
			'title' => 'Theater',
			'type' => 'relationship',
			'name_field' => 'name',
			'constraints' => array('film' => 'theaters') // theaters는 Film 모델의 관계 메서드 이름과 일치합니다.
		),
	)

이렇게 하면 특정 영화를 선택했을 때 해당 영화를 상영한 극장들로 선택 범위가 제한됩니다. 반대로 특정 극장을 선택하면 해당 극장에서 상영된 영화만 선택할 수 있게 됩니다.
