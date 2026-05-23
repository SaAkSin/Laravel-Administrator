# 필드 타입 - WYSIWYG

- [사용법](#usage)

<a name="usage"></a>
## 사용법

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-wysiwyg.png" />

`wysiwyg` 필드 타입은 데이터베이스에서 TEXT 타입이어야 합니다.

	'entry' => array(
		'type' => 'wysiwyg',
		'title' => 'Entry',
	)

수정 폼에서 관리자 사용자에게 CKEditor WYSIWYG가 표시됩니다. 필드가 데이터베이스에 저장될 때 생성된 HTML이 TEXT 필드에 저장됩니다.

WYSIWYG는 크기가 상당히 크므로, [모델의 폼 너비](/docs/ko/model-configuration#form-width)를 `400` 또는 `500` 정도로 확장하는 것을 고려해 보시는 것이 좋습니다.
