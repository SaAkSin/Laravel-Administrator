# 소개

- [개요](#overview)
- [커스터마이징](#customization)
- [인증](#authentication)
- [Eloquent](#eloquent)
- [설치 / 가이드](#installation-guidance)

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/overview.jpg" />

<a name="overview"></a>
## 개요

Administrator는 [Laravel](http://laravel.com)을 위한 관리자 인터페이스 빌더입니다. Administrator를 사용하면 Eloquent 모델과 그 관계들을 시각적으로 관리할 수 있으며, 사이트 데이터를 저장하고 사이트 작업을 수행하기 위한 독립적인 설정 페이지를 생성할 수도 있습니다.

각 Eloquent 모델에 대해 관리자가 편집할 수 있는 필드, 결과 테이블에 표시할 컬럼, 커스텀 액션 버튼, 그리고 사용할 수 있는 필터를 정의할 수 있습니다. 이러한 필드는 "belongsTo" 및 "belongsToMany" 관계일 수도 있으며("hasOne" 및 "hasMany" 관계는 지원하지 않음), 이를 통해 사용자가 사이트의 데이터 관계를 손쉽게 관리할 수 있도록 지원합니다.


<a name="authentication"></a>
## 인증

다른 많은 관리자 인터페이스 시스템과 달리, Administrator에는 자체 인증 기능이 내장되어 있지 않습니다. 기존 시스템 위에 불필요한 추가 인증 레이어를 제공하는 대신, Administrator는 여러분이 이미 사용 중인 기존 인증 시스템과 직접 연동됩니다. "permission" 익명 함수를 활용하여 현재 사용자에게 특정 리소스에 대한 접근 권한이 있는지 여부를 기존 인증 시스템을 통해 직접 결정할 수 있습니다.


<a name="eloquent"></a>
## Eloquent

가장 중요한 점은 Administrator가 [Eloquent ORM](http://laravel.com/docs/eloquent)을 염두에 두고 설계되었다는 것입니다. 따라서 접근자(Accessors), 설정자(Mutators), 이벤트(Events) 등 일반적인 Eloquent 기능을 사용하는 데 아무런 방해를 받지 않습니다.

> Administrator에서 Eloquent 모델을 설정하는 자세한 방법은 [모델 설정 문서](/docs/model-configuration)를 참고해 주십시오.


<a name="settings-pages"></a>
## 설정 페이지

검증 규칙(Validation rules), 필드 및 액션을 직접 정의할 수 있는 단순한 설정 페이지를 원하신다면, 그렇게 하실 수 있습니다! 사이트의 온라인/오프라인 여부를 결정하는 체크박스나 "캐시 지우기" 버튼 등, 필드나 커스텀 액션을 임의로 조합하여 설정 페이지를 만드는 것은 매우 쉽습니다.

> 설정 페이지를 생성하는 자세한 방법은 [설정(Settings) 구성 문서](/docs/settings-configuration)를 참고해 주십시오.


<a name="installation-guidance"></a>
## 설치 / 가이드

Administrator를 시작하려면 [설치 가이드](/docs/installation)를 확인해 주십시오.

도움이 필요하시다면 [튜토리얼](/docs/tutorials)을 확인해 주십시오.
