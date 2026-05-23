# 필드 타입 - 마크다운 (Markdown)

- [사용법](#usage)

<a name="usage"></a>
## 사용법

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-markdown.png" />

`markdown` 필드 타입은 데이터베이스의 텍스트(text) 계열의 모든 타입에 매핑될 수 있습니다.

```php
	'name' => array(
		'type' => 'markdown',
		'title' => 'Name',
		'limit' => 300, // 선택 사항, 기본값은 제한 없음
		'height' => 130, // 선택 사항, 기본값은 100
	)
```

수정 폼에서 관리자는 왼쪽에 텍스트 영역(textarea)을, 오른쪽에는 해당 텍스트의 마크다운 렌더링(HTML 마크업) 결과를 보게 됩니다. 필드 값이 데이터베이스에 저장될 때는 렌더링된 HTML이 아니라 작성된 마크다운 원본 텍스트가 저장됩니다.

`limit` 옵션을 사용하면 필드의 글자 수 제한을 설정할 수 있습니다.

`height` 옵션을 사용하면 텍스트 영역의 높이를 픽셀(px) 단위로 설정할 수 있습니다.

`markdown` 필드 타입은 넓은 레이아웃 공간이 필요하므로, [모델의 폼 너비(form width) 확장](/docs/ko/model-configuration#form-width)을 참고하여 `400` 정도로 설정하는 것을 권장합니다.
