# 필드 타입 - Password

`password`는 비밀번호 입력 전용 필드입니다. 기존 값은 화면에 표시하지 않으며, 기본적으로 setter 필드로 동작합니다.

```php {2}
'password' => array(
    'type' => 'password',
    'title' => '비밀번호',
);
```

비밀번호 해시는 Eloquent mutator 또는 모델 이벤트에서 처리하십시오.

```php {5}
protected function password(): Attribute
{
    return Attribute::make(
        set: fn ($value) => filled($value) ? bcrypt($value) : $this->password,
    );
}
```
