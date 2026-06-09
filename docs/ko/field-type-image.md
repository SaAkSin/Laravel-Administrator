# 필드 타입 - 이미지 (Image)

- [사용법](#usage)

<a name="usage"></a>
## 사용법

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-image.jpg" />

`image` 필드 타입은 데이터베이스에서 텍스트 기반 타입이어야 합니다. 이미지의 *파일명*이 이 필드에 저장되며, 원본은 지정한 `location` 경로에 저장됩니다. 그리고 크기가 조정된 사본들은 `sizes` 옵션에서 정의한 경로에 저장됩니다.

	'image' => array(
		'title' => 'Image',
		'type' => 'image',
		'location' => public_path() . '/uploads/products/originals/',
		'naming' => 'random',
		'length' => 20,
		'size_limit' => 2,
		'display_raw_value' => false,
		'sizes' => array(
			array(65, 57, 'crop', public_path() . '/uploads/products/thumbs/small/', 100),
			array(220, 138, 'landscape', public_path() . '/uploads/products/thumbs/medium/', 100),
			array(383, 276, 'fit', public_path() . '/uploads/products/thumbs/full/', 100)
		)
	)

수정 폼에서 관리자 사용자에게 이미지 업로더가 표시됩니다. 현재로서는 이 업로더를 통해 한 번에 하나의 이미지만 업로드할 수 있습니다.

필수 항목인 `location` 옵션을 사용하여 원본 이미지가 저장될 위치를 정의할 수 있습니다.

선택 항목인 `naming` 옵션을 사용하면 파일명을 그대로 유지할지(`keep`) 아니면 파일명을 무작위로 생성할지(`random`) 정의할 수 있습니다. 파일명 충돌을 방지하기 위해 기본값은 `random`으로 설정되어 있지만, 이를 `keep`으로 설정하면 업로드하는 이미지의 원본 파일명을 유지할 수 있습니다.

선택 항목인 `length` 옵션은 `naming` 옵션의 값으로 `random`이 설정된 경우 생성될 파일명의 길이를 정의할 수 있게 해줍니다.

선택 항목인 `size_limit` 옵션을 사용하여 메가바이트(MB) 단위의 정수 값으로 크기 제한을 설정할 수 있습니다. 이 옵션은 자바스크립트(JavaScript) 파일 업로드 대화 상자에만 영향을 미치며, PHP의 업로드 크기 제한 자체를 제한하지는 않습니다.

선택 항목인 `display_raw_value` 옵션을 사용하면 저장된 이미지 소스 문자열의 원본 값(raw value)을 이미지 입력란에 입력할 수 있습니다. 이 옵션은 접근자(Accessor), 변경자(Mutator) 및 [`setter 필드`](/docs/fields#setter-option)를 사용하여 로컬 서버에 이미지를 저장하지 않고 원격 공용 이미지 서버에 업로드하는 경우에 유용합니다.

선택 항목인 `sizes` 옵션을 사용하면 원하는 만큼 이미지 크기 조정을 정의할 수 있습니다. 포맷은 `array([가로], [세로], [조정 방식], [저장 경로], [품질])`입니다. 지원되는 다양한 조정 방식(`method`)은 `exact`, `portrait`, `landscape`, `fit`, `auto`, `crop`입니다.
