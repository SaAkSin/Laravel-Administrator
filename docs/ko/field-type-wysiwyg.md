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

수정 폼에서 관리자 사용자에게 CKEditor 4 WYSIWYG(Full Spec Toolbar)가 표시됩니다. 필드가 데이터베이스에 저장될 때 생성된 HTML이 TEXT 필드에 저장됩니다.

> [!NOTE]
> `wysiwyg` 타입은 패키지에 로컬로 내장된 **CKEditor 4** (Full Spec)를 사용합니다. 더 가볍고 현대적인 에디터를 사용하고 싶다면 **Quill** 에디터를 사용하는 [위지윅2](/docs/ko/field-type-wysiwyg2) (`type => 'wysiwyg2'`) 필드 타입을 사용할 수 있습니다.

WYSIWYG는 크기가 상당히 크므로, [모델의 폼 너비](/docs/ko/model-configuration#form-width)를 `400` 또는 `500` 정도로 확장하는 것을 고려해 보시는 것이 좋습니다.
