# 설치 (Installation)

- [Composer](#composer)
- [Laravel 4](#laravel-4)
- [Laravel 3](#laravel-3)
- [에셋 (Assets)](#assets)
- [Administrator 설정 (Administrator Config)](#administrator-config)
- [모델 설정 (Model Config)](#model-config)
- [세팅 설정 (Settings Config)](#settings-config)

<a name="composer"></a>
## Composer

Laravel 10.x와 함께 사용할 Administrator를 설치하려면 다음 명령어를 실행하십시오:

```sh
composer require "saaksin/laravel-administrator:^10.6"
```

설치가 완료되면 `config/app.php` 파일의 `providers` 배열에 서비스 프로바이더(Service Provider)를 등록합니다 (라라벨의 패키지 자동 검색 기능이 활성화되어 있다면 자동 등록되므로 수동 등록을 생략할 수 있습니다):

```php
'providers' => [
    ...
    SaAkSin\Administrator\AdministratorServiceProvider::class,
]
```

그런 다음 아래 명령어를 통해 패키지의 설정 파일 및 컴파일된 Vite 프론트엔드 에셋을 호스트 프로젝트로 퍼블리시(게시)합니다.

```sh
# 설정 파일 및 에셋 전체 강제 퍼블리시 (Vite 에셋 및 CKEditor 4 번들 포함)
php artisan vendor:publish --tag=laravel-administrator --force
```

이 명령을 실행하면 프로젝트 루트에 `config/administrator.php` 설정 파일이 추가되고, `public/packages/saaksin/administrator/dist/` 경로에 배포용 에셋들이 복사됩니다.


<a name="laravel-4"></a>
## Laravel 4

Laravel 4에서 Administrator를 사용하려면 Administrator 4 버전을 지정해야 합니다:

```json
"frozennode/administrator": "4.*"
```

그 다음 `php artisan config:publish frozennode/administrator` 명령어로 설정 파일을 퍼블리시하십시오. 이 명령을 실행하면 `app/config/packages/frozennode/administrator/administrator.php` 파일이 추가됩니다.

마지막으로 `php artisan asset:publish frozennode/administrator` 명령어를 사용하여 패키지의 에셋을 퍼블리시해야 합니다.

<a name="laravel-3"></a>
## Laravel 3

Administrator가 Composer로 전환되었기 때문에 더 이상 `php artisan bundle:install administrator` 또는 `php artisan bundle:upgrade administrator` 명령어를 사용할 수 없습니다. Laravel 3에서 Administrator를 사용하려면 [3.3.2 브랜치](https://github.com/FrozenNode/Laravel-Administrator/tree/3.3.2)로 전환하여 다운로드한 뒤, 이를 `/bundles/administrator` 디렉토리에 추가하고 `bundles.php` 파일에 다음 코드를 추가해야 합니다:

```php
'administrator' => array(
    'handles' => 'admin', // 이 번들이 사용할 URI를 결정합니다.
    'auto' => true,
),
```

<a name="assets"></a>
## 에셋 (Assets) 및 배포 자동화

패키지가 업데이트될 때마다 최신 빌드 에셋 자산이 호스트로 동기화되도록 퍼블리시 명령을 실행해야 합니다:

```sh
php artisan vendor:publish --tag=laravel-administrator --force
```

이 작업을 수동으로 처리하는 대신, 호스트 프로젝트의 `composer.json` 파일 내 `scripts` 객체에 아래와 같이 추가하여 자동화할 수 있습니다:

```json
"scripts": {
	"post-install-cmd": [
		"php artisan clear-compiled",
		"php artisan vendor:publish --tag=laravel-administrator --force"
	],
	"post-update-cmd": [
		"php artisan clear-compiled",
		"php artisan vendor:publish --tag=laravel-administrator --force"
	]
}
```

<a name="administrator-config"></a>
## Administrator 설정 (Administrator Config)

다음 명령어로 설정 파일을 퍼블리시할 수 있습니다:

	php artisan config:publish frozennode/administrator

이 명령을 실행하면 `app/config/packages/frozennode/administrator/administrator.php` 파일이 생성되고 몇 가지 기본값으로 채워집니다. 이 [설정 파일(config file)](http://administrator.frozennode.com/docs/configuration)은 Administrator와 상호작용하는 기본적인 방법입니다.

Laravel 3 번들을 설치한 경우 `bundles/administrator/config/administrator.php` 파일을 직접 수정하거나, `application/config` 디렉토리에 `administrator.php` 파일을 새로 생성할 수 있습니다.

설정 파일에는 반드시 제공해야 하는 몇 가지 필수 필드가 있습니다. 그중 하나인 `menu` 옵션에서는 사이트의 메뉴 구조를 정의하고 모델 설정 파일을 가리키도록 설정합니다.

> 모든 설정 옵션에 대한 자세한 설명은 **[설정 문서(configuration docs)](/docs/configuration)**를 참조하십시오.


<a name="model-config"></a>
## 모델 설정 (Model Config)

모든 Eloquent 모델(또는 최종적으로 Eloquent 모델을 상속받는 모든 객체)은 모델 설정 파일로 표현될 수 있습니다. 이 파일들은 애플리케이션 디렉토리 구조 내부의 어느 곳에나 보관할 수 있으며, 메인 `administrator.php` 설정 파일에서 (`model_config_path` 옵션을 통해) 해당 파일들의 경로를 제공하면 됩니다. 이 파일들의 이름은 `administrator.php` 설정의 `menu` 옵션에 제공된 값과 일치해야 합니다.

모델 설정 파일이 정상적으로 작동하려면 반드시 제공해야 하는 몇 가지 필수 필드가 있습니다. 그 외에도 모델별로 관리자 인터페이스를 커스터마이징하는 데 도움을 주는 다양한 선택 필드를 정의할 수 있습니다. 예를 들어, 특정 모델에 WYSIWYG 필드가 필요한 경우 일반적으로 수정 폼의 너비를 기본값보다 넓게 지정하고 싶을 것입니다. 이때 해당 모델 설정에서 `form_width` 옵션을 설정해주기만 하면 됩니다.

> 모든 모델 설정 옵션에 대한 자세한 설명은 **[모델 설정 문서(model configuration docs)](/docs/model-configuration)**를 참조하십시오.


<a name="settings-config"></a>
## 세팅 설정 (Settings Config)

세팅 설정 파일은 Eloquent 모델로 표현하기에 가장 적절하지 않은 관리적 옵션들을 관리하는 데 유용합니다. 이 파일들은 애플리케이션 디렉토리 구조 내부의 어느 곳에나 보관할 수 있으며, 메인 `administrator.php` 설정 파일에서 (`settings_config_path` 옵션을 통해) 해당 파일들의 경로를 제공하면 됩니다. 이 파일들의 이름은 `administrator.php` 설정의 `menu` 옵션에 제공된 값과 일치해야 합니다.

세팅 설정 파일이 정상적으로 작동하려면 반드시 제공해야 하는 몇 가지 필수 필드가 있습니다. 그 외에도 세팅 페이지를 커스터마이징하는 데 도움을 주는 다양한 선택 필드를 정의할 수 있습니다.

> 모든 세팅 설정 옵션에 대한 자세한 설명은 **[세팅 설정 문서(settings configuration docs)](/docs/settings-configuration)**를 참조하십시오.
