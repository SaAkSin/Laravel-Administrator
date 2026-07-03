---
title: "프로젝트 안정화 기준선 복구 결과 보고서"
date: 2026-07-03
author: "executor"
status: "review"
description: "Laravel Administrator 패키지의 테스트 실패 및 TS6 컴파일 오류, deprecation 경고 해결 및 마크다운 XSS 조치를 포함한 보완 결과 보고서"
---

# 프로젝트 안정화 기준선 복구 결과 보고서

## 1. 개요
본 작업은 `saaksin/laravel-administrator` 패키지의 PHPUnit 테스트 실패 오류를 수정하고, TypeScript 6 및 PHP 8.3 환경에서 발생하는 컴파일/런타임 deprecation 경고들을 말끔히 해소하여 배포 가능한 품질 기준선을 복구하는 데 목적이 있습니다. 

1차 검수 요청 이후 발생한 마크다운 미리보기 XSS 취약점 조치 지시사항에 따라, 외부 라이브러리 의존성을 배제하고 `marked` 파서의 커스텀 렌더러 기능을 통해 HTML 태그를 차단하는 보안 대책을 보강했습니다.

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

### 2차 보완 이행 내역 (XSS 보강)
- **마크다운 XSS 방지 커스텀 렌더러 도입**:
  - [app.ts](file:///Users/galahan/SaAkSin/artgrammer/laravel-administrator/resources/js/app.ts) 파일에 `marked.use()`를 연동하여 `html` 토큰 파싱 시 HTML Entity로 escape 처리하도록 커스텀 HTML 렌더러를 탑재했습니다.
  - 이를 통해 날것의 `<script>` 및 `<img onerror>` 같은 악성 스크립트 실행이 완전히 원천 차단됩니다. (마크다운 고유의 굵게, 기울임, 링크 및 이미지 마크업 렌더링은 안전하게 동작합니다.)

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
  # 결과: built in 1.17s (정상 종료)
  ```

## 5. 남은 리스크
- **CSS Gradient 경고**: `npm run build` 시 CSS Gradient의 구형 direction 구문(`right`) 경고가 몇몇 레거시 CSS 파일에서 발생합니다. 런타임 레이아웃이나 기능에는 지장을 주지 않으나, 장기적인 에셋 리팩토링 차원에서 후속 정리 대상으로 기록해 둡니다.

## 6. 검수 요청 사항
- **대상 브랜치**: `dev`
- **커밋 해시**: c44550b5
- **보완 이력**:
  - `458f63b9` (1차 검수 요청 커밋)
  - `c44550b5` (2차 마크다운 미리보기 XSS 방어 보완 완료 커밋)
