# 유효성 검사

- [소개](#introduction)
- [모델 설정의 rules](#model-rules)
- [모델 정적 rules](#static-rules)
- [Form Request](#form-request)
- [세팅 설정의 rules](#settings-rules)
- [사용자 정의 메시지](#custom-messages)

<a name="introduction"></a>
## 소개

Administrator는 Laravel validation 규칙을 사용해 모델 저장과 세팅 저장 데이터를 검증합니다. 검증 실패 시 저장하지 않고 관리자 화면에 오류를 반환합니다.

<a name="model-rules"></a>
## 모델 설정의 rules

모델 설정 파일에 `rules`와 `messages`를 직접 정의할 수 있습니다.

```php {2-5}
return array(
    'rules' => array(
        'name' => 'required|string|max:255',
        'email' => 'required|email',
    ),
    'messages' => array(
        'name.required' => '이름을 입력하십시오.',
    ),
);
```

설정 파일의 `rules`가 있으면 모델 정적 속성보다 우선합니다.

<a name="static-rules"></a>
## 모델 정적 rules

설정 파일에 `rules`를 두지 않으면 Eloquent 모델의 정적 `$rules`, `$messages`를 사용할 수 있습니다.

```php {4-7}
class User extends Model
{
    public static $rules = array(
        'name' => 'required|string|max:255',
        'email' => 'required|email',
    );

    public static $messages = array(
        'email.email' => '올바른 이메일 주소를 입력하십시오.',
    );
}
```

<a name="form-request"></a>
## Form Request

모델 설정에는 Laravel Form Request 클래스를 지정할 수 있습니다.

```php {2}
return array(
    'form_request' => App\Http\Requests\Admin\UserSaveRequest::class,
);
```

저장 요청에서 Form Request 검증 오류가 있으면 Administrator는 저장을 중단하고 오류를 JSON 응답으로 반환합니다. Form Request의 `authorize()`와 `rules()`는 일반 Laravel 요청 검증과 같은 방식으로 작성합니다.

<a name="settings-rules"></a>
## 세팅 설정의 rules

세팅 설정 파일도 같은 형식의 `rules`, `messages`를 지원합니다.

```php {2-5}
return array(
    'rules' => array(
        'site_name' => 'required|max:50',
        'admin_email' => 'required|email',
    ),
    'messages' => array(
        'site_name.required' => '사이트 이름을 입력하십시오.',
    ),
);
```

<a name="custom-messages"></a>
## 사용자 정의 메시지

메시지 키는 Laravel의 사용자 정의 메시지 형식을 따릅니다.

```php
'messages' => array(
    'name.required' => '이름 필드는 필수입니다.',
    'email.email' => '이메일 형식이 올바르지 않습니다.',
);
```

모델 설정과 세팅 설정의 전체 옵션은 [모델 설정 문서](./model-configuration.md#validation)와 [세팅 설정 문서](./settings-configuration.md#validation)를 참고하십시오.
