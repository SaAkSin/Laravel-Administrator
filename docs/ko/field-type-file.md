# 필드 타입 - 파일 (File)

- [사용법](#usage)

<a name="usage"></a>
## 사용법

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-file.png" />

`file` 필드 타입은 데이터베이스에서 텍스트 계열(text-like) 타입이어야 합니다. 파일의 이름이 이 필드에 저장되며, 원본 파일은 지정한 `location`에 저장됩니다. 크기가 조정된 복사본(이미지 등의 경우)은 `sizes` 옵션에 정의한 위치에 저장됩니다.

```php
	'media_document' => array(
		'title' => '파일', // 화면에 표시될 필드 라벨
		'type' => 'file', // 필드 타입은 'file'
		'location' => storage_path() . '/media_documents/', // 파일이 저장될 경로 (필수)
		'naming' => 'random', // 파일명 저장 방식: 'random'(무작위) 또는 'keep'(원본 유지)
		'length' => 20, // 'naming'이 'random'일 때 무작위 파일명의 길이
		'size_limit' => 2, // 업로드 크기 제한 (메가바이트 단위)
		'display_raw_value' => false, // 원본 값을 파일 링크에 그대로 노출할지 여부
		'mimes' => 'pdf,psd,doc', // 허용할 MIME 타입 확장자 목록
	)
```

수정 폼에서 관리자 사용자에게 파일 업로더가 표시됩니다.

필수 항목인 `location` 옵션을 사용하면 파일이 저장될 위치를 정의할 수 있습니다.

선택 항목인 `naming` 옵션은 파일 이름을 그대로 `keep`(유지)할지 아니면 `random`(무작위)으로 생성할지 정의합니다. 기본값은 이름 충돌을 피하기 위해 `random`으로 설정되어 있지만, 이를 `keep`으로 설정하면 원본 파일 이름을 그대로 유지할 수 있습니다.

선택 항목인 `length` 옵션은 `naming` 옵션에 `random`이 설정된 경우 파일 이름의 길이를 정의할 수 있게 해줍니다.

선택 항목인 `size_limit` 옵션은 메가바이트(MB) 단위의 정수 크기 제한을 설정할 수 있게 해줍니다. 이는 JavaScript 파일 업로드 대화 상자에만 영향을 미치며, PHP 업로드 제한 크기(php.ini에서 설정 가능)를 제한하지는 않습니다.

선택 항목인 `display_raw_value` 옵션은 저장된 파일 소스 문자열의 원본(raw) 값을 표시되는 파일 링크에 삽입하도록 합니다. 이 옵션은 로컬 서버에 파일을 저장하는 대신 접근자(Accessor), 설정자(Mutator) 및 [`setter 필드`](/docs/fields#setter-option)를 사용하여 원격 공용 파일 서버에 업로드하는 경우에 유용합니다.

선택 항목인 `mimes` 옵션은 기본적으로 모든 파일 형식을 허용합니다. 이 옵션은 라라벨의 [mimes 유효성 검사](http://laravel.com/docs/validation#rule-mimes)를 사용하며, 이는 내부적으로 PHP Fileinfo 확장을 사용하여 파일 콘텐츠를 읽고 실제 MIME 타입을 결정합니다.
