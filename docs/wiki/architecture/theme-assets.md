# 테마와 에셋 로딩

## 설정 계약

[기본 설정](../../../src/config/administrator.php)은 `theme`, `themes`, `custom_css`, `custom_js`를 제공한다.

- 기본 활성 테마는 `silver`다.
- `themes` 항목은 표시 이름과 Vite manifest 엔트리를 연결한다.
- `legacy`는 별도 테마 엔트리가 없어 공통 스타일만 사용한다.
- 등록되지 않은 테마 또는 manifest에 없는 엔트리는 `silver`로 fallback한다.
- `custom_css`와 `custom_js`는 패키지 기본 에셋 뒤에 추가한다.

사용자 설정 예시는 [한국어 설정 매뉴얼](../../ko/configuration.md#themes-and-assets)과 [영문 설정 매뉴얼](../../configuration.md#themes-and-assets)에 유지한다.

## 빌드 계약

[Vite 설정](../../../vite.config.js)은 다음 두 엔트리를 `public/dist`에 빌드한다.

- `resources/js/app.ts`: 공통 JavaScript와 공통 CSS
- `resources/css/themes/silver.css`: `silver` 테마 스타일

프로덕션에서는 `.vite/manifest.json`을 우선 사용하고, 이전 형식인 `manifest.json`도 fallback으로 읽는다. HMR 환경에서는 `public/dist/hot`의 개발 서버 주소를 사용한다.

## 로딩 순서

[view composer](../../../src/viewComposers.php)의 레이아웃 구성 순서는 다음과 같다.

1. 공통 애플리케이션 CSS와 JavaScript
2. 선택한 테마 CSS
3. `custom_css`
4. `custom_js`

테마 엔트리를 추가할 때는 설정의 `themes` 항목과 Vite input을 함께 갱신해야 한다. manifest에 엔트리가 없으면 사용자 설정만으로 테마가 활성화되지 않는다.

## 검증 위치

[AdministratorIntegrationTest](../../../tests/AdministratorIntegrationTest.php)는 서비스 프로바이더와 라우트, `silver`, `legacy`, 잘못된 테마 fallback, 사용자 CSS·JavaScript 로딩을 검증한다. 에셋 구조를 바꾸면 관련 통합 테스트와 `npm run build`를 함께 실행한다.
