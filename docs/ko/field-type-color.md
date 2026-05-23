# 필드 타입 - Color

- [사용법](#usage)

<a name="usage"></a>
## 사용법

`color` 필드 타입은 데이터베이스에서 VARCHAR 또는 TEXT 필드여야 합니다.

	'hex' => array(
		'type' => 'color',
		'title' => 'Color',
	)

수정 폼에서 관리자 사용자에게는 # 기호로 시작하는 16진수(hex) 값으로 텍스트 필드를 채워주는 컬러 피커(color picker)가 표시됩니다.

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-color.png" />
