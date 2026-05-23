# Laravel Administrator

기존 FrozenNode의 `Laravel-Administrator` 패키지가 더 이상 업데이트되지 않아, 이를 바탕으로 현대적인 라라벨(Laravel 10.x) 및 최신 프론트엔드 아키텍처 환경에 최적화하여 개발 및 업그레이드하고 있는 관리자 페이지 빌더 패키지입니다.

Administrator를 사용하면 Eloquent 모델과 관계(Relations)를 선언적으로 관리할 수 있으며, 독립적인 사이트 설정 페이지나 대시보드 커스텀 작업을 손쉽게 구축할 수 있습니다.

- **Author:** 이기석 (GiSeok Lee)
- **Website:** [https://github.com/SaAkSin/Laravel-Administrator](https://github.com/SaAkSin/Laravel-Administrator)
- **Version:** `10.5.0`

[![Build Status](https://travis-ci.org/FrozenNode/Laravel-Administrator.png?branch=master)](https://travis-ci.org/FrozenNode/Laravel-Administrator)

<img src="https://raw.github.com/SaAkSin/Laravel-Administrator/master/examples/images/overview.jpg" />

---

## 🚀 1. 주요 현대화 및 개선 사항 (10.5.0)

레거시 의존성을 탈피하고 프론트엔드 및 빌드 환경을 대대적으로 현대화했습니다.

### 🎨 프론트엔드 아키텍처 전면 개편 (Vite + Alpine.js v3 + Tailwind CSS)
* **의존성 간소화**: 구시대의 `Knockout.js` 및 `jQuery` 환경을 완전히 제거하고, 가볍고 빠른 **Alpine.js v3**와 유연한 **Tailwind CSS**, 현대적 에셋 번들러인 **Vite** 체계로 마이그레이션했습니다.
* **성능 극대화**: 초기 에셋 로딩 크기를 60% 이상 절감하였고, 렌더링 성능과 INP(Interaction to Next Paint) 속도를 2배 이상 향상시켰습니다.
* **프리미엄 & 반응형 UI/UX**: 모바일부터 와이드 모니터까지 가로 스크롤 없이 부드럽게 대응하는 완전 반응형 CSS Grid/Flexbox 레이아웃을 제공합니다. 세련된 HSL 기반의 Slate/Indigo 컬러 팔레트와 부드러운 Glassmorphism 효과, 그리고 미려한 다크 모드(Dark Mode)를 기본 제공합니다.

### 📦 서드파티 라이브러리 경량화
* **Combobox**: 무거운 `Select2` 대신 Alpine.js 기반의 경량 커스텀 Combobox 컴포넌트를 사용하여 관계형 데이터 자동완성 검색을 제공합니다.
* **업로더**: 기존 `Plupload` 대신 HTML5 기본 API를 활용한 바닐라 멀티 업로더를 탑재했습니다.
* **에디터**: 기존 WYSIWYG 에디터를 최신 **Quill** 및 **Marked**(마크다운 파서) 라이브러리로 대체하여 세련되고 가벼운 콘텐츠 편집 환경을 마련했습니다.

---

## 📋 2. 시스템 요구사항

* **PHP:** `^8.1` 이상
* **Laravel Framework:** `10.x`

---

## 🛠️ 3. 설치 방법 (Composer)

라라벨 10 에서 composer 를 사용하여 쉽게 설치할 수 있습니다.

```sh
composer require "saaksin/laravel-administrator:10.5.*"
```

설치 후, `config/app.php` 의 `providers` 배열에 서비스 프로바이더를 등록합니다.

```php
'providers' => [
    ...
    SaAkSin\Administrator\AdministratorServiceProvider::class
]
```

이 후, `php artisan vendor:publish` 을 실행합니다. `config/administrator.php` [설정파일](https://github.com/SaAkSin/Laravel-Administrator/blob/dev-10/docs/configuration.md)이 추가 되고, public 디렉토리에 관련 에셋, 뷰, 언어 파일 등이 복사됩니다. 

> [!IMPORTANT]  
> 설정 파일들은 config 디렉토리 하위가 아닌, **프로젝트 루트 디렉토리**의 `administrator/`, `administrator/settings/`에 위치합니다.

---

## ⚙️ 4. 설정 파일 구성 (Configuration)

### 📄 설정파일 정의
설정파일명과 동일한 함수명으로 시작합니다. 가령, 설정 파일이 `users.php` 이라면, 반드시 `users` 함수를 통하여 설정(배열)을 반환합니다. (이를 통해 세션 등과 연계하여 조건에 따른 동적 배열 결과를 반환할 수 있습니다.)

```php
// administrator/users.php
function users()
{
    return array(
        'title' => '사용자 관리',
        'single' => '사용자',
        'model' => App\Models\User::class,
        
        // 데이터 목록에 노출할 컬럼 정의
        'columns' => array(
            'id' => array(
                'title' => 'ID',
            ),
            'name' => array(
                'title' => '이름',
            ),
            'email' => array(
                'title' => '이메일',
            ),
        ),

        // 데이터 필터링 조건 정의
        'filters' => array(
            'name' => array(
                'title' => '이름 검색',
                'type'  => 'text',
            ),
        ),

        // 등록 및 수정 폼 필드 구성
        'edit_fields' => array(
            'name' => array(
                'title' => '이름',
                'type'  => 'text',
            ),
            'email' => array(
                'title' => '이메일',
                'type'  => 'text',
            ),
            'password' => array(
                'title' => '비밀번호',
                'type'  => 'password',
            ),
        ),
    );
}
```

### 🎨 사용자 정의 js 파일 지정
`config/administrator.php` 에 `custom_js` 항목을 추가하여 커스텀 스크립트를 로드할 수 있습니다.

```php
'custom_js' => array(
    'custom' => asset('js/custom.js'),
)
```

---

## ✨ 5. 제공되는 확장 및 부가 기능

### 🔐 HTTPS 환경 연동
`app/Providers/AppServiceProvider.php` 에서 라우트의 경로가 https 가 되도록 지정합니다.
```php
use Illuminate\Routing\UrlGenerator;

public function boot(UrlGenerator $url)
{
    if (app()->environment('production')) {
        $url->forceScheme('https');
    }
}
```

asset url 에 https 주소를 사용하도록 `.env` 에 `ASSET_URL` 을 지정합니다.
```env
ASSET_URL=https://도메인주소
```

### 🔍 MySQL FULL TEXT 검색 지원
filter 에서 MySQL 의 full text 검색을 지원합니다. (대용량 테이블 검색에 유리합니다.)

```php
'filters' => array(
    'no' => array(
        'title' => 'Number',
        'type' => 'fulltext_mysql'
    ),
),
```

### ⚡ TEXT 빠른 검색 (Quick Search Filter)
filter 에서 시작 단어 검색 및 포커스 아웃 이벤트 시 즉시 검색을 시작하는 유용한 필터 타입입니다.

```php
'filters' => array(
    'no' => array(
        'title' => 'Name',
        'type' => 'text_quick'
    ),
),
```

### 🔄 페이지 리로드 (Reload Page)
액션을 성공적으로 실행한 후, 현재 페이지를 리로드하는 기능을 제공합니다.

```php
'action' => array(
    'reload' => true
),
```

### 💾 VIEW 모델 지원 (실험 중)
모델 설정에서 view 모델 여부를 설정할 수 있습니다. (아직은 실험적인 기능이며, 조회 시 데이터베이스 로드를 최적화하여 약간의 성능 개선을 기대할 수 있습니다. MySQL InnoDB 환경 권장)

```php
'view' => true
```

---

## 🚫 6. 레거시 프레임워크 지원 중단 안내

### Laravel 4
더 이상 지원하지 않습니다.

### Laravel 3
더 이상 지원하지 않습니다.

---

## 🛠️ 7. 에셋 개발 및 빌드 (Developer Guide)

패키지의 프론트엔드 소스코드(`resources/js`, `resources/css` 등)를 직접 수정하여 기여하거나 커스터마이징하고 싶다면 패키지 루트 디렉토리에서 다음 NPM 명령어를 활용해 주세요.

```bash
# 의존성 설치
npm install

# Vite 개발 서버 실행 (핫 리로드 지원)
npm run dev

# 배포용 최적화 에셋 빌드 (public/dist 디렉토리에 빌드됨)
npm run build
```

---

## 📖 8. Documentation

Administrator에 대한 원본 전체 문서는 [http://administrator.frozennode.com](http://administrator.frozennode.com)에서 확인할 수 있습니다. 또한, 이 패키지의 루트 디렉토리에 있는 [docs/](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/docs) 디렉토리에서도 한국어 설명 문서를 포함한 상세 가이드를 찾아보실 수 있습니다.

---

## 📄 9. Copyright and License

Administrator was written by Jan Hartigan of Frozen Node for the Laravel framework.  
It is currently developed, modernized, and maintained by GiSeok Lee (SaAkSin).  
Administrator is released under the MIT License. See the [LICENSE](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/LICENSE) file for details.

---

## 📝 10. Recent Changelog

### 10.5.0
- **프론트엔드 아키텍처 현대화 (Vite + Alpine.js + Tailwind CSS) 전면 개편**
- 기존 레거시 `Knockout.js` 및 `jQuery` 기반 코드 완전 제거
- `Select2` ➡️ Alpine Combobox, `Plupload` ➡️ HTML5 바닐라 멀티 업로더 교체
- `Quill` 및 `Marked` 마크다운 컴포넌트 연동 지원

### 10.0.0
- 라라벨 10.0.0 지원

### 5.8.1
- Added: View 모델 지원(alpha), MySQL InnoDB desc 정렬 속도 이슈 대응

### 5.8.0
- 라라벨 5.8.0 지원
- Added: FULL TEXT 검색(MySQL), https 지원

### 5.1.0
- 모델 파일내 세션 사용가능
