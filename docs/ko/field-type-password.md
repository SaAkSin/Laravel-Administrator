# 필드 타입 - Password (비밀번호)

- [사용법](#usage)

<a name="usage"></a>
## 사용법

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-password.png" />

`password` 필드 타입은 데이터베이스의 텍스트 형태(text-like)인 모든 타입에 대응할 수 있습니다. 비밀번호 필드 타입은 자동으로 세터(setter)로 생성되므로 값을 화면에 표시하지 않습니다(즉, 기존 값이 표시되지 않습니다). 입력된 비밀번호가 올바르게 해싱(hashing)되도록 하려면, 비밀번호 필드와 함께 [Eloquent mutators](http://laravel.com/docs/eloquent#accessors-and-mutators)를 사용해 주십시오.

	'password' => array(
		'type' => 'password',
		'title' => 'Password',
	)

수정 폼(edit form)에서 관리자 사용자에게는 비밀번호를 입력할 수 있는 입력란이 제공됩니다.
