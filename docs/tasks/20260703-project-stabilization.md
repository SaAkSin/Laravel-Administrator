---
title: "프로젝트 안정화 기준선 복구"
date: 2026-07-03
author: "software-architect"
status: "ready"
description: "Laravel Administrator 패키지의 테스트/타입 검사 실패와 문서-구현 불일치를 해소하고 배포 전 품질 기준선을 복구한다."
---

# 프로젝트 안정화 기준선 복구

## 목적

Laravel 10용 `saaksin/laravel-administrator` 패키지의 현재 검증 실패와 운영 품질 리스크를 해소한다.

이 작업의 1차 목표는 다음 두 검증 명령이 성공하는 상태를 만드는 것이다.

```bash
./vendor/bin/phpunit
./node_modules/.bin/tsc --noEmit
```

## 배경

프로젝트 분석 결과 다음 문제가 확인되었다.

- PHPUnit 실행 시 283개 테스트 중 1개가 오류로 실패한다.
- TypeScript 6 환경에서 `moduleResolution: "Node"` 설정이 폐기 오류를 발생시킨다.
- PHP 8.3 기준 deprecation 경고가 여러 위치에서 발생한다.
- README는 jQuery/Select2 제거를 설명하지만 런타임은 여전히 jQuery, jQuery UI, Select2 CDN을 로드한다.
- 운영 번들 대상 TypeScript 코드에 디버그 `console.log`가 남아 있다.
- `x-html` 출력 지점의 sanitizer 정책과 회귀 테스트가 명확하지 않다.
- Composer 설정이 dev 안정성에 열려 있어 배포 재현성 리스크가 있다.

## 작업 범위

- PHPUnit 실패 원인 수정
  - `SaAkSin\Administrator\Fields\Factory::filterQueryBySelectedItems()`의 현재 구현과 테스트 더블 사이의 불일치를 해소한다.
  - 핵심 정렬 동작이 회귀하지 않도록 테스트 기대값을 보강한다.
- TypeScript 설정 수정
  - Vite 기반 프론트엔드에 맞게 `tsconfig.json`의 `moduleResolution`을 현대 설정으로 조정한다.
  - 단순 경고 무시보다 장기 유지 가능한 설정을 우선한다.
- PHP 8.3 deprecation 정리
  - 선택 파라미터 순서 문제를 수정한다.
  - `null` 입력이 PHP 내장 함수로 전달되는 검증 코드를 방어적으로 수정한다.
  - Eloquent 동적 속성 할당 deprecation 발생 지점을 점검하고 필요한 경우 `setAttribute()` 계열로 정리한다.
- 문서와 런타임 정합성 정리
  - jQuery/Select2가 실제로 필요 없으면 `src/viewComposers.php`의 CDN 로드를 제거한다.
  - 아직 필요하면 README와 관련 문서에서 “완전 제거” 표현을 실제 상태에 맞게 수정한다.
- 프론트엔드 운영 로그 정리
  - `resources/js/controllers/AdminController.ts`
  - `resources/js/controllers/RelationSelectController.ts`
  - 운영 환경에서 불필요한 `console.log`가 출력되지 않도록 제거하거나 개발 환경 조건으로 제한한다.
- HTML 출력 보안 정책 보강
  - `x-html`로 출력되는 컬럼 렌더링, 마크다운 미리보기, 콤보박스 하이라이트 출력이 어떤 기준으로 신뢰되는지 확인한다.
  - 필요한 경우 sanitizer 적용 또는 문서화/테스트를 추가한다.
- Composer 안정성 정책 검토
  - `minimum-stability: dev`와 `mockery/mockery: 2.0.x-dev`의 필요성을 확인한다.
  - 가능하면 `prefer-stable: true` 추가 또는 안정 버전 제약으로 전환한다.

## 제외 범위

- Laravel 11 이상 지원 범위 확대는 포함하지 않는다.
- 전체 UI 재설계는 포함하지 않는다.
- CKEditor 4 제거 또는 Quill 단일화 같은 에디터 정책 변경은 포함하지 않는다.
- 기존 관리자 기능의 신규 기능 추가는 포함하지 않는다.
- 원격 배포, Packagist 배포, 릴리스 태깅은 포함하지 않는다.

## 구현 지시

- 변경 전 `git status --short --branch`로 작업트리 상태를 확인한다.
- 기존 패키지 구조와 네이밍을 유지한다.
- 문제 해결 순서는 다음을 권장한다.
  - PHPUnit 실패 수정
  - TypeScript 설정 수정
  - PHP deprecation 정리
  - 문서/런타임 정합성 정리
  - 프론트엔드 디버그 로그 정리
  - 보안 출력 정책 테스트 보강
  - Composer 안정성 정책 검토
- 예상 변경 파일은 다음과 같다.
  - `src/SaAkSin/Administrator/Fields/Factory.php`
  - `tests/Fields/FieldFactoryTest.php`
  - `tsconfig.json`
  - `src/SaAkSin/Administrator/Config/Model/Config.php`
  - `src/SaAkSin/Administrator/Validator.php`
  - `src/SaAkSin/Administrator/Fields/Relationships/BelongsTo.php`
  - `src/viewComposers.php`
  - `resources/js/controllers/AdminController.ts`
  - `resources/js/controllers/RelationSelectController.ts`
  - `readme.md`
  - 필요한 경우 `docs/` 하위 문서
- `src/viewComposers.php`에서 jQuery/Select2를 제거할 경우 실제 화면에서 필요한 레거시 기능이 깨지지 않는지 반드시 확인한다.
- Composer 안정성 정책 변경은 `composer.lock` 변화가 크면 별도 이슈로 분리한다.

## UX/UI 지시

- 관리자 리스트, 필터, 편집 폼, 관계형 콤보박스의 기존 사용 흐름을 유지한다.
- 빈 목록, 로딩 중, 오류 메시지, 권한 없음 상태의 기존 표시 흐름을 깨지 않도록 한다.
- 디버그 로그 제거가 사용자 피드백 메시지나 오류 표시를 제거하는 방식이 되어서는 안 된다.
- 프론트엔드 변경 후 빌드 산출물은 필요한 경우에만 갱신한다. 산출물을 갱신했다면 결과보고서에 이유를 기록한다.

## 보안 지시

- `x-html` 사용 지점은 신뢰 경계가 명확해야 한다.
- 서버에서 렌더링된 HTML을 그대로 출력하는 경우 기존 `clean()` 연동 또는 동등한 sanitizer 적용 여부를 확인한다.
- 마크다운 미리보기는 XSS 가능성을 기준으로 검토한다.
- 파일/에셋 경로 접근 보강 로직은 유지한다.
- 인증/인가 우회가 발생하지 않도록 관리자 미들웨어와 모델별 permission 흐름을 건드릴 때는 회귀 테스트를 추가한다.
- 민감정보나 토큰이 로그에 출력되지 않도록 한다.

## 테스트 지시

필수 실행:

```bash
./vendor/bin/phpunit
./node_modules/.bin/tsc --noEmit
```

가능하면 추가 실행:

```bash
npm run build
```

테스트 보강 기준:

- `filterQueryBySelectedItems()`는 선택 항목 우선 조회, 정렬 필드, accessor/mutator 필드 회피 동작을 검증해야 한다.
- PHP 8.3 deprecation 정리 대상은 경고가 재발하지 않도록 최소 단위 테스트를 추가하거나 기존 테스트를 보강한다.
- `x-html` 출력 보안 정책을 변경했다면 XSS성 입력이 실행 가능한 HTML로 노출되지 않는 케이스를 검증한다.
- jQuery/Select2 로드를 제거했다면 관계형 필터와 편집 필드의 옵션 선택, 자동완성, 선택 해제 동작을 검증한다.

## 완료 기준

- [ ] `./vendor/bin/phpunit`이 실패 없이 통과한다.
- [ ] `./node_modules/.bin/tsc --noEmit`이 실패 없이 통과한다.
- [ ] PHP 8.3 deprecation 경고가 현재 작업 범위 안에서 해소되었거나 남은 경고가 결과보고서에 사유와 후속 계획으로 기록된다.
- [ ] README의 프론트엔드 의존성 설명과 실제 런타임 로드 목록이 서로 모순되지 않는다.
- [ ] 운영 번들 대상 코드에 불필요한 디버그 `console.log`가 남아 있지 않다.
- [ ] `x-html` 출력 지점의 신뢰 경계, sanitizer 여부, 테스트 여부가 결과보고서에 명확히 기록된다.
- [ ] 변경 범위와 테스트 결과가 GitHub Issue comment에 기록된다.

## 결과보고서 경로

Arti Engineer는 작업 완료 후 다음 경로에 결과보고서를 작성한다.

```text
docs/tasks/20260703-project-stabilization-report.md
```

결과보고서에는 다음을 포함한다.

- 변경 요약
- 변경 파일 목록
- 테스트 실행 결과
- 미실행 테스트와 사유
- 보안 검토 결과
- 문서 정합성 확인 결과
- 남은 리스크와 후속 이슈 제안
- 커밋 SHA와 브랜치

## 이슈 분리 기준

다음 조건 중 하나에 해당하면 별도 Issue로 분리한다.

- Composer 안정성 정책 변경으로 `composer.lock` 대규모 변경이 발생한다.
- jQuery/Select2 제거가 화면 동작 변경 또는 대규모 마크업 수정으로 확장된다.
- `x-html` sanitizer 정책 변경이 컬럼 렌더링 계약 변경으로 이어진다.
- Laravel 11 이상 지원, CKEditor 4 제거, 에디터 정책 변경 요구가 발생한다.
