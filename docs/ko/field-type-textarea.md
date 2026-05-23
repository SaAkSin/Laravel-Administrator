# 필드 타입 - Textarea (텍스트 영역)

- [사용법](#usage)

<a name="usage"></a>
## 사용법

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-textarea.png" />

`textarea` 필드 타입은 데이터베이스의 텍스트와 유사한 모든 타입에 사용할 수 있습니다.

	'name' => array(
		'type' => 'textarea',
		'title' => 'Name',
		'limit' => 300, // 선택 사항, 기본값은 제한 없음
		'height' => 130, // 선택 사항, 기본값은 100
	)

수정 폼에서 관리자 사용자에게 텍스트 영역(textarea)이 표시됩니다.

`limit` 옵션을 사용하면 필드의 글자 수 제한을 설정할 수 있습니다.

`height` 옵션을 사용하면 텍스트 영역의 높이를 픽셀 단위로 설정할 수 있습니다.
