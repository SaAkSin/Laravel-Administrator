# 관계 컬럼 (Relationship Columns)

- [소개](#introduction)
- [Eloquent 관계 설정](#setting-up-the-eloquent-relationship)
- [간단한 Select](#simple-select)
- [더 복잡한 Select](#more-complex-selects)
- [중첩된 관계](#nested-relationships)

<a name="introduction"></a>
## 소개

> **참고**: 이 문서는 관계 컬럼에 대해서만 다룹니다. 모든 컬럼 옵션에 대해 더 자세히 알아보려면 [컬럼 문서](./columns.md)를 확인하세요.

적당히 복잡한 데이터베이스라면 어떤 테이블의 컬럼이 다른 테이블의 ID를 나타내는 경우가 많습니다. 관리자 사용자에게 이 ID를 그대로 보여주는 것은 대부분의 경우 매우 무의미합니다. 컴퓨터에게는 숫자가 의미 있지만 사람에게는 그렇지 않기 때문입니다. 또는, 관계가 모델의 테이블에 직접 표현되지 않고 두 테이블을 연결하는 피벗 테이블(pivot table)에 있거나 다른 모델 테이블의 컬럼으로 존재할 수도 있습니다.

만약 관련된 컬럼을 표시하고 싶다면, `relationship` 옵션을 제공하면 됩니다. 이 옵션의 값은 *해당 모델에 정의된 Eloquent 관계(relationship)의 메서드 이름*이어야 합니다. 이와 더불어, Administrator가 관계 테이블에서 값을 가져올 때 사용할 `select` 옵션을 함께 제공해야 합니다.

<a name="setting-up-the-eloquent-relationship"></a>
## Eloquent 관계 설정

[Eloquent 관계](http://laravel.com/docs/eloquent#relationships)는 관계 메서드를 사용해 일반적인 방식으로 설정해야 합니다. 예를 들어 다음과 같이 설정할 수 있습니다:

```php
class User extends Eloquent {

	public function phone()
	{
		return $this->hasOne('Phone');
	}
}
```

이 경우, 우리가 참조하고자 하는 관계 "이름"은 `phone` (메서드 이름)입니다. 또 다른 예시는 다음과 같습니다:

```php
class Director extends Eloquent {

	public function films()
	{
		return $this->belongsToMany('Film');
	}
}
```

이 경우, 참조하려는 관계 "이름"은 `films`입니다.

Administrator는 관계에 설정된 모든 조건부 필터(conditional filter)를 준수합니다. 이는 특히 `hasMany` 또는 `belongsToMany` 관계에서 유용합니다. 예를 들어, 관계를 다음과 같이 설정한 경우:

```php
public function alerts()
{
	return $this->hasMany('Alert')->whereNotified(false); // 알림이 전송되지 않은(unnotified) 알림만 가져옴
}
```

그리고 select를 사용해 알림의 개수를 세면, `notified` 컬럼의 값이 `0`인 알림만 계산됩니다.

<a name="simple-select"></a>
## 간단한 Select

결합하려는 데이터가 반드시 단 하나의 행만 갖는 경우에 간단한 `select` 구문을 사용합니다. 이는 관계가 `belongsTo` 또는 `hasOne` 관계로 정의될 때 해당합니다. 예를 들어, `Hat` 모델이 나타내는 `hats` 테이블이 있다고 가정해 봅시다. 각 모자는 단 한 명의 `User` 소유이므로, `hats` 테이블에는 `user_id` 컬럼이 존재합니다. Administrator에서 `Hat` 모델을 표시할 때, 다음과 같이 모자 소유자의 이메일 주소를 컬럼으로 표시할 수 있습니다:

```php
'user_email' => array(
	'title' => "소유자 이메일",
	'relationship' => 'user', // Eloquent 관계 메서드의 이름입니다!
	'select' => "(:table).email",
)
```

만약 사용자의 이름(First Name)과 성(Last Name)을 함께 표시하고 싶다면 다음과 같이 할 수 있습니다:

```php
'user_name' => array(
	'title' => "소유자 이름",
	'relationship' => 'user', // Eloquent 관계 메서드의 이름입니다!
	'select' => "CONCAT((:table).first_name, ' ', (:table).last_name)",
)
```

<a name="more-complex-selects"></a>
## 더 복잡한 Select

만약 `hasMany` 또는 `belongsToMany` 관계에서 데이터를 보여주려면, `select` 구문에 그룹화 함수(grouping function)를 제공하고 싶을 수 있습니다. `Director` 모델이 있고 그가 참여한 영화의 개수를 세고 싶다면 다음과 같이 할 수 있습니다:

```php
'num_films' => array(
	'title' => '영화 수',
	'relationship' => 'films', // Eloquent 관계 메서드의 이름입니다!
	'select' => "COUNT((:table).id)",
)
```

만약 `Film` 모델에서 포맷팅된 박스오피스 총 수익을 보여주고 싶다면 다음과 같이 할 수 있습니다:

```php
'box_office' => array(
	'title' => '박스오피스 수익',
	'relationship' => 'boxOffice', // Eloquent 관계 메서드의 이름입니다!
	'select' => "CONCAT('$', FORMAT(SUM((:table).revenue), 2))",
)
```

`select` 옵션에 올바른 SQL SELECT 구문을 입력하기만 하면, 원하는 방식으로 자유롭게 컬럼을 표시할 수 있는 강력한 기능을 활용할 수 있습니다.

<a name="nested-relationships"></a>
## 중첩된 관계

때로는 멀리 연결된(distantly-related) 모델의 컬럼 값을 표시하고 싶을 수도 있습니다. 특히 일련의 `belongsTo` 관계들이 이어져 있을 때 그렇습니다. 예를 들어, `cart` 테이블이 있다고 상상해 봅시다. 이 테이블에는 `inventory` 테이블을 가리키는 `inventory_id`가 있습니다. 그리고 `inventory` 테이블에는 `products` 테이블을 가리키는 `product_id`가 있습니다. 모델은 다음과 같이 작성되어 있을 것입니다:

### Cart 모델
```php
public function inventory()
{
	return $this->belongsTo('Inventory');
}
```

### Inventory 모델
```php
public function product()
{
	return $this->belongsTo('Product');
}
```

각 상품(Product)에는 이름이 있으며, `cart` 관리자 페이지의 행에서 이를 선택하여 보여주려고 합니다. 이를 처리하기 위해, [Eloquent가 중첩된 관계를 즉시 로드(Eager Loading)할 때 사용하는](http://laravel.com/docs/eloquent#eager-loading) 점(dot) 표기법을 사용할 수 있습니다.

```php
'product_name' => array(
	'title' => '상품명',
	'relationship' => 'inventory.product',
	'select' => '(:table).name', // products.name을 선택합니다.
)
```

이러한 중첩에는 제한이 없으므로, 상품의 카테고리명을 가져오고 싶다면 다음과 같이 할 수 있습니다:

```php
'category_name' => array(
	'title' => '카테고리명',
	'relationship' => 'inventory.product.category',
	'select' => '(:table).name', // categories.name을 선택합니다.
)
```
