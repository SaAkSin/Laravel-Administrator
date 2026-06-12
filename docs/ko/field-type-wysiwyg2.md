# 필드 타입 - 위지윅2 (WYSIWYG2)

- [사용법](#usage)

<a name="usage"></a>
## 사용법

`wysiwyg2` 필드 타입은 데이터베이스에서 TEXT 타입이어야 합니다.

	'entry' => array(
		'type' => 'wysiwyg2',
		'title' => 'Entry',
	)

수정 폼에서 관리자 사용자에게 현대적인 Quill WYSIWYG 에디터가 표시됩니다. 필드가 데이터베이스에 저장될 때 생성된 HTML이 TEXT 필드에 저장됩니다.

> [!NOTE]
> `wysiwyg2` 타입은 가볍고 세련된 **Quill** 에디터를 사용합니다. 테이블 지원 및 로우 HTML 소스코드 편집 모드 등 다양한 고급 기능이 포함된 클래식 에디터를 선호하신다면 **CKEditor 4**를 사용하는 [위지윅](/docs/ko/field-type-wysiwyg) (`type => 'wysiwyg'`) 필드 타입을 사용할 수 있습니다.

WYSIWYG는 크기가 상당히 크므로, [모델의 폼 너비](/docs/ko/model-configuration#form-width)를 `400` 또는 `500` 정도로 확장하는 것을 고려해 보시는 것이 좋습니다.
