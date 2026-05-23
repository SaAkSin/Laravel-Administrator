# 커스텀 액션 (Custom Actions)

- [소개](#introduction)
- [모델 설정](#model-config)
- [설정 구성](#settings-config)
- [확인 메시지](#confirmations)
- [동적 메시지](#dynamic-messages)

<a name="introduction"></a>
## 소개

관리자에게 커스텀 코드를 실행할 수 있는 버튼을 제공하고 싶다면, [모델](/docs/model-configuration#custom-actions) 또는 [설정 구성 파일](/docs/settings-configuration#custom-actions)에 커스텀 액션을 정의할 수 있습니다. Eloquent 모델을 수정하거나, 설정 페이지에서 사이트 캐시를 지우거나 데이터베이스를 백업하는 버튼을 사용자에게 제공할 수 있습니다. 커스텀 액션은 구성 파일의 `actions` 배열의 일부로 정의되며 다음과 같은 형태를 가집니다:

	/**
	 * 모델의 커스텀 액션을 정의하는 곳입니다.
	 */
	'actions' => array(
		// 사이트 캐시 비우기
		'clear_cache' => array(
			'title' => '캐시 비우기',
			'messages' => array(
				'active' => '캐시 비우는 중...',
				'success' => '캐시가 비워졌습니다!',
				'error' => '캐시를 비우는 동안 오류가 발생했습니다.',
			),
			// 설정 데이터가 이 함수로 전달되며, 참(truthy) 값이 반환되면 저장됩니다.
			'action' => function(&$data)
			{
				Cache::flush();

				// 성공 메시지를 띄우려면 true를 반환합니다.
				// 기본 오류 메시지를 띄우려면 false를 반환합니다.
				// 커스텀 오류를 보여주려면 문자열(string)을 반환합니다.
				// 파일 다운로드를 시작하려면 Response::download()를 반환합니다.
				return true;
			}
		),
	),

`title` 옵션을 사용하면 버튼의 레이블 값을 정의할 수 있습니다.

`messages` 옵션은 `active`, `success`, `error`라는 세 가지 키를 가진 배열입니다. `active` 키는 액션이 실행되는 동안 사용자에게 보여줄 메시지입니다. `success` 키는 성공 메시지입니다. `error` 키는 기본 오류 메시지입니다.

`permission` 옵션은 관련 `$model`을 유일한 매개변수로 전달받는 익명 함수입니다. 이는 이 액션을 [`action_permissions`](/docs/model-configuration#action-permissions) 배열에 넣는 것과 완전히 동일합니다. 권한 콜백을 어디에 둘지는 전적으로 여러분의 선택에 달려 있습니다.

> **참고**: 커스텀 오류 메시지를 보여주고 싶다면, `action` 함수에서 오류 문자열을 반환하십시오. 파일 다운로드를 시작하고 싶다면, `Response::download()`를 반환하십시오.

<a name="model-config"></a>
## 모델 설정

[모델 설정 파일](/docs/model-configuration#custom-actions)에서, 해당 항목의 Eloquent 모델 인스턴스가 `action` 함수로 전달됩니다.

	'action' => function(&$model)
	{
		//
	}

모델 페이지의 `global_actions` 배열에 일반적인 액션을 생성할 수도 있습니다.

	'global_actions' => array(
		'some_action' => array(
			// 액션 옵션
		)
	)

이러한 전역 커스텀 액션에는 필터링된 쿼리 빌더(query builder) 객체가 전달되므로, 원하는 경우 현재 결과 집합을 대상으로 작업을 수행할 수 있습니다. 또한 이를 사용하여 게시되지 않은 모든 항목을 게시하거나, 알림을 받지 못한 사용자에게 이메일을 보내는 등 생각할 수 있는 모든 작업을 수행할 수 있습니다.

<a name="settings-config"></a>
## 설정 구성

[설정 구성 파일](/docs/settings-configuration#custom-actions)에서는 해당 페이지에 현재 저장되어 있는 데이터가 `action` 함수에 참조(&)로 전달됩니다.

	'action' => function(&$data)
	{
		//
	}

<a name="confirmations"></a>
## 확인 메시지

액션을 실행하기 전에 확인 대화 상자를 띄우고 싶다면, 액션에 `confirmation` 옵션을 전달할 수 있습니다:

	'clear_cache' => array(
		'title' => '캐시 비우기',
		'confirmation' => '정말로 캐시를 비우시겠습니까?',
		'action' => function(&$data)
		{
			// 캐시 비우기
		}
	),

관리자 사용자가 승인하면 액션이 진행됩니다. 승인하지 않으면 액션이 진행되지 않습니다.

<a name="dynamic-messages"></a>
## 동적 메시지

커스텀 액션의 텍스트 필드(`title`, `confirmation`, 그리고 `messages` 배열의 모든 키)에 익명 함수를 전달할 수 있습니다. 이 익명 함수들에는 관련 Eloquent 모델 또는 설정 구성 객체가 매개변수로 전달됩니다. 예를 들면 다음과 같습니다:

	'ban_user' => array(
		'title' => function($model)
		{
			return $model->name . " 사용자를 정말로 " . ($model->banned ? '차단 해제' : '차단') . "하시겠습니까?";
		},
		'messages' => array(
			'active' => function($model)
			{
				return $model->name . " 사용자를 " . ($model->banned ? '차단 해제하는 중' : '차단하는 중') . "...";
			},
			'success' => function($model)
			{
				return $model->name . " 사용자가 " . ($model->banned ? '차단 해제되었습니다!' : '차단되었습니다!');
			},
			'error' => function($model)
			{
				return $model->name . " 사용자를 " . ($model->banned ? '차단 해제' : '차단') . "하는 동안 오류가 발생했습니다.";
			},
		),
		'action' => function(&$data)
		{
			// 사용자 차단 또는 차단 해제 처리
		}
	),
	
<a name="reload"></a>
## 리로드

완료 후, 현재 페이지를 리로드 할 수 있습니다.

    'action' => array(
        'reload' => true,
        
    ),
