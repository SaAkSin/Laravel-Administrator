# 유효성 검사 (Validation)

- [소개](#introduction)
- [사용자 정의 메시지](#custom-messages)
- [Aware 사용하기](#using-aware)

<a name="introduction"></a>
## 소개 (Introduction)

Administrator는 모델의 유효성을 검사하기 위해 [Laravel의 유효성 검사(Laravel's validation)](http://laravel.com/docs/validation)를 사용합니다. 설정 파일에서 [`rules`](/docs/model-configuration#validation-rules) 옵션을 제공할 수 있습니다:

	'rules' => array(
		'name' => 'required',
		'age' => 'required|integer|min:18',
	)

 또는 모델 페이지의 경우, 다음과 같이 Eloquent 모델에 static `$rules` 속성을 정의할 수 있습니다:

	class Movie extends Eloquent {

		/**
		 * 유효성 검사 규칙
		 */
		public static $rules = array(
			'name' => 'required',
			'age' => 'required|integer|min:18',
		);
	}

이제 관리자 사용자가 나이(age) 없이 혹은 18세 미만인 상태로 영화(Movie) 모델을 저장하려고 하면, Administrator가 사용자에게 오류를 알리고 저장이 수행되지 않도록 차단합니다.

<a name="custom-messages"></a>
## 사용자 정의 메시지 (Custom Messages)

사용자에게 제공하는 각 모델에 대해 사용자 정의 유효성 검사 메시지를 사용해야 할 가능성이 높습니다. 이를 위해 설정 파일에 [`messages`](/docs/model-configuration#validation-messages) 옵션을 제공할 수 있습니다:

	'messages' => array(
		'name.required' => '이름 필드는 필수입니다',
		'age.min' => '최소 나이는 18세입니다',
	)

또는 모델 페이지의 경우, 다음과 같이 Eloquent 모델에 static `$messages` 속성을 정의할 수 있습니다:

	class Movie extends Eloquent {

		/**
		 * 유효성 검사 규칙
		 */
		public static $messages = array(
			'name.required' => '이름 필드는 필수입니다',
			'age.min' => '최소 나이는 18세입니다',
		);
	}

<a name="using-aware"></a>
## Aware 사용하기 (Using Aware)

이미 [Aware](https://github.com/awareness/aware)를 사용하고 계시다면 따로 하실 작업이 없습니다! Aware를 사용하면 Eloquent 모델에 static `$rules` 속성을 정의할 수 있으며, 이는 Administrator에서 작동하는 방식과 정확히 일치합니다.
