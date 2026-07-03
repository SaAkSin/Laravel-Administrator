---
title: "프로젝트 안정화 기준선 복구 결과 보고서"
date: 2026-07-03
author: "executor"
status: "review"
description: "Laravel Administrator 패키지의 테스트 실패 및 TS6 컴파일 오류, deprecation 경고 해결 및 마크다운 XSS 차단 최종 조치(운영 격리 완료)를 포함한 5차 보완 결과 보고서"
---

# 프로젝트 안정화 기준선 복구 결과 보고서

## 1. 개요
본 작업은 `saaksin/laravel-administrator` 패키지의 PHPUnit 테스트 실패 오류를 수정하고, TypeScript 6 및 PHP 8.3 환경에서 발생하는 컴파일/런타임 deprecation 경고들을 말끔히 해소하여 배포 가능한 품질 기준선을 복구하는 데 목적이 있습니다. 

검수 요청 이후 발생한 마크다운 미리보기 XSS 취약점 조치 지시사항에 따라, 외부 라이브러리 의존성을 배제하고 `marked` 파서의 커스텀 렌더러 기능을 통해 HTML 태그를 차단하는 보안 대책을 보강했습니다. 최종 5차 보완에서는 실시간 회귀 테스트의 성공 로그 및 상시 구동 코드가 운영(Production) 빌드 번들에 포함되지 않도록 Vite 환경 변수를 활용해 개발 환경으로 안전하게 격리 처리했습니다.

## 2. 작업 이행 내역

### 1차 이행 내역
- **PHPUnit 테스트 정합성 복구**: [FieldFactoryTest.php](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/tests/Fields/FieldFactoryTest.php)의 `testFilterQueryBySelectedItems` 모의 객체(Mock) 설정을 보강하고, accessor/mutator 필드 회피 동작 및 정렬 필드에 대한 다차원적 분기 검증 테스트 케이스들을 추가 작성했습니다.
- **PHP 8.3 Deprecation 경고 해소**: 
  - [Config.php](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/src/SaAkSin/Administrator/Config/Model/Config.php)의 `getModel()` 시그니처 필수 변수화
  - [BelongsToTest.php](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/tests/Fields/Relationships/BelongsToTest.php) 내 `BelongsToEloquentStub`에 `public $rel_id`를 선언하여 dynamic property 할당 에러 방지
  - [Validator.php](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/src/SaAkSin/Administrator/Validator.php)의 `validateDirectory` 및 `validateEloquent`에 `is_string` 타입 가드 추가
- **TS6 컴파일 오류 해결**:
  - [tsconfig.json](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/tsconfig.json)의 `moduleResolution`을 `"bundler"`로 수정
  - [global.d.ts](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/resources/js/types/global.d.ts)에 CSS 모듈 정의를 삽입 및 누락 타입 보강
  - `AdminController` 및 `RelationSelectController` 클래스의 프록시 객체 `self` 참조에 올바른 클래스 타입을 캐스팅하여 제네릭 함수 호출 오류 전면 해결
- **레거시 의존성 CDN 제거**:
  - [viewComposers.php](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/src/viewComposers.php)에서 불필요하게 CDN으로 주입되던 `jquery`, `jquery-ui`, `select2` 로딩 코드를 제거 (CKEditor는 유지)
- **프론트엔드 최적화 및 보안 보강**:
  - [AdminController.ts](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/resources/js/controllers/AdminController.ts) 및 [RelationSelectController.ts](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/resources/js/controllers/RelationSelectController.ts) 내 디버그용 `console.log` 전량 삭제
  - [RelationSelectController.ts](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/resources/js/controllers/RelationSelectController.ts)의 `highlight` 함수에 HTML-escape 처리를 도입하여 드롭다운 드롭 시 발생 가능한 Stored XSS 차단
  - [composer.json](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/composer.json)에 `"prefer-stable": true` 추가

### 2차 및 3차 보완 이행 내역
- **마크다운 XSS 방지 커스텀 렌더러 도입**:
  - [app.ts](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/resources/js/app.ts) 파일에 `marked.use()`를 연동하여 `html` 토큰 파싱 시 HTML Entity로 escape 처리하도록 커스텀 HTML 렌더러를 탑재했습니다.
  - 마크다운 링크/이미지 프로토콜을 세척하여 `javascript:`, `data:` 등의 스키마가 로딩되는 클릭 기반 XSS를 필터링했습니다.

### 4차 보완 이행 내역 (Sanitizer 정교화 및 XSS 회귀 검증)
- **마크다운 URL Sanitizer 정교화**:
  - [app.ts](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/resources/js/app.ts) 파일에 `decodeHtmlEntities` (HTML Entity 우회 방어), `normalizeUrlForPolicy` (제어문자 및 공백 우회 제거), `hasExplicitScheme`, `sanitizeUrl`을 추가 구현하고 허용 프로토콜 allowlist를 강제했습니다.
- **Attribute Injection 방어**:
  - `marked` 의 custom `link` 및 `image` 렌더러에 의해 반환되는 HTML 태그의 모든 속성(`href`, `src`, `title`, `alt`) 값을 최종적으로 `escapeHtml()` 처리하여 속성 탈출(따옴표 주입 XSS)을 원천 차단했습니다.

### 5차 보완 이행 내역 (운영 번들 빌드 최적화 및 회귀 검증 격리)
- **성공 로그 제거 및 개발 서버 한정 격리**:
  - `runMarkdownXssRegressionTests()` 내부에 출력되던 성공 확인용 `console.log` 메시지를 전량 삭제했습니다.
  - 로딩 시점 즉시 실행부는 Vite의 환경변수 `import.meta.env.DEV` 분기를 통해 조건부 기동하게 수정했습니다. 이에 따라 프로덕션 컴파일(`npm run build`) 시 해당 테스트 기동 코드가 Dead Code로 인식되어 운영 번들(`public/dist/js/app-*.js`) 내부에서 원천 탈락(제거)되도록 최적화했습니다.

## 3. 변경 파일
- [global.d.ts](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/resources/js/types/global.d.ts)
- [app.ts](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/resources/js/app.ts)
- [AdminController.ts](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/resources/js/controllers/AdminController.ts)
- [RelationSelectController.ts](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/resources/js/controllers/RelationSelectController.ts)
- [tsconfig.json](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/tsconfig.json)
- [Config.php](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/src/SaAkSin/Administrator/Config/Model/Config.php)
- [Validator.php](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/src/SaAkSin/Administrator/Validator.php)
- [viewComposers.php](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/src/viewComposers.php)
- [FieldFactoryTest.php](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/tests/Fields/FieldFactoryTest.php)
- [BelongsToTest.php](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/tests/Fields/Relationships/BelongsToTest.php)
- [composer.json](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/composer.json)

## 4. 테스트 결과
- **PHPUnit 테스트**: 286개 테스트 전체 통과 (오류 0, Deprecation 경고 0)
  ```bash
  ./vendor/bin/phpunit
  # 결과: OK (286 tests, 249 assertions)
  ```
- **TypeScript 타입 컴파일 검증**: 오류 없이 정상 완료
  ```bash
  ./node_modules/.bin/tsc --noEmit
  # 결과: 성공 (에러 메시지 없음)
  ```
- **Vite 프론트엔드 빌드**: 빌드 자체는 성공적이나 CSS gradient 방향 구문 경고가 식별됨
  ```bash
  npm run build
  # 결과: built in 1.06s (정상 종료)
  ```

## 5. 남은 리스크
- **CSS Gradient 경고**: `npm run build` 시 CSS Gradient의 구형 direction 구문(`right`) 경고가 몇몇 레거시 CSS 파일에서 발생합니다. 런타임 레이아웃이나 기능에는 지장을 주지 않으나, 장기적인 에셋 리팩토링 차원에서 후속 정리 대상으로 기록해 둡니다.

## 6. 마크다운 XSS 회귀 검증 결과 (Regression Test Outputs)

| 주입된 취약성 입력 구문 (Input) | 렌더링된 안전한 HTML 결과 (Output) | 검증 결과 (Status) |
| :--- | :--- | :--- |
| `<img src=x onerror=alert(1)>` | `&lt;img src=x onerror=alert(1)&gt;` | **PASS (이벤트/태그 무력화)** |
| `[x](javascript:alert(1))` | `<a href="#">x</a>` | **PASS (javascript 스키마 차단)** |
| `[x](javascript&#58;alert(1))` | `<a href="#">x</a>` | **PASS (HTML Entity 우회 디코딩 차단)** |
| `[x](java\nscript:alert(1))` | `[x](java\nscript:alert(1))` (줄바꿈 스키마는 링크 파싱이 무력화되어 일반 텍스트 노출됨) | **PASS (비링크 일반 문자열 안전 노출)** |
| `[x](data:text/html,<script>alert(1)</script>)` | `<a href="#">x</a>` | **PASS (data 스키마 차단)** |
| `![x](data:image/svg+xml,<svg onload=alert(1)>)` | `<img src="#" alt="x" />` | **PASS (이미지 data 스키마 차단)** |
| `[x](vbscript:msgbox(1))` | `<a href="#">x</a>` | **PASS (vbscript 스키마 차단)** |
| `[x](https://example.com/"onclick="alert(1))` | `<a href="https://example.com/&quot;onclick=&quot;alert(1)">x</a>` | **PASS (따옴표 속성 이탈 방어)** |

## 7. 검수 요청 사항
- **대상 브랜치**: `dev`
- **커밋 해시**: 96444437
- **보완 이력**:
  - `458f63b9` (1차 검수 요청 커밋)
  - `81063847` (2차 마크다운 HTML 토큰 XSS 방어 보완 커밋)
  - `d8a40046` (3차 마크다운 링크/이미지 프로토콜 XSS 방어 보완 커밋)
  - `82d37a28` (4차 URL Sanitizer 정교화 및 DOMParser 회귀 테스트 탑재 커밋)
  - `96444437` (5차 운영 번들 회귀 테스트 격리 및 성공 로그 억제 완료 커밋)
