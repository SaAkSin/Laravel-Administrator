# 개발 검증 기준

검증 범위는 변경 경로와 완료 기준에 직접 연결된 테스트를 기본으로 한다. 전체 테스트는 작업지시서, 저장소 정책 또는 공통 기반 변경 때문에 관련 테스트만으로 회귀 범위를 한정할 수 없을 때 실행한다.

## 기본 명령

| 변경 영역 | 명령 |
| --- | --- |
| Composer 메타데이터 | `composer validate --strict` |
| PHP 동작 | `./vendor/bin/phpunit <관련 테스트>` |
| 공통 PHP 계약 | `./vendor/bin/phpunit` |
| TypeScript | `./node_modules/.bin/tsc --noEmit` |
| 제품 에셋 | `npm run build` |
| 공개 매뉴얼과 VitePress | `npm run docs:build` |
| Wiki·raw 영향 | Arti Docs `lint_docs.py` |

[PHPUnit 설정](../../../phpunit.xml)은 `Test.php` suffix를 가진 테스트를 탐색한다. [TypeScript 설정](../../../tsconfig.json)은 Vite 환경에 맞는 `bundler` module resolution을 사용한다.

## 장기 실행 워커 검증

Octane 호환성 변경은 실제 서버 바이너리에 결합하지 않고 두 수준으로 검증한다. scoped binding 단위 테스트는 Laravel container의 lifecycle 종료를 직접 재현하고, 통합 테스트는 개발 의존성의 `Laravel\Octane\Worker`와 공식 `FakeClient`로 애플리케이션을 한 번 boot한 뒤 요청을 두 번 연속 처리한다.

- 같은 lifecycle에서 모든 `admin_*` scoped 서비스와 `itemconfig`가 같은 인스턴스를 반환하는지 확인한다.
- `forgetScopedInstances()` 단위 경계와 Worker handle-twice 통합 경계에서 sandbox, 설정, 필드·컬럼·액션 cache가 새 인스턴스로 교체되는지 확인한다.
- 첫 요청의 모델 permission, action permission, 필터 `value`·`min_value`·`max_value`를 변경한 뒤 두 번째 Worker 요청에서 다른 사용자·모델 값과 필터 초기값이 사용되는지 확인한다.
- 서로 다른 세션의 페이지당 행 수가 lifecycle마다 해당 세션 값으로 계산되는지 확인한다.
- 모델·설정 middleware를 연속 실행해 scoped 등록 목록이 증가하지 않고 각 라우트의 `itemconfig`가 한 번만 생성되는지 확인한다.
- dashboard, custom page와 secure asset처럼 `itemconfig`가 없는 경로가 설정을 해석하지 않는지 확인한다.
- 호스트 사용자 정의 validation resolver와 확장을 등록한 뒤 관리자 validator를 해석해도 일반 validation이 같은 resolver를 계속 사용하는지 확인한다.
- 허용 로케일 세션, 다른 세션, 빈 값과 허용되지 않은 값을 순차 적용해 각 요청의 세션 값 또는 `app.locale`이 사용되는지 확인한다.

공용 container binding과 validation factory 계약을 바꾸는 작업은 관련 lifecycle 테스트 뒤 전체 PHPUnit suite를 실행한다.

## 결과 기록

Pull Request body에는 다음을 남긴다.

- 실행 명령과 종료 코드
- 관련 테스트를 선택한 근거
- 전체 테스트를 실행했다면 필요한 이유
- 미실행 검증과 환경 제약
- Wiki·raw 영향과 lint 결과

고정된 테스트 개수나 과거 실행 시간은 Wiki에 기록하지 않는다. 해당 값은 구현과 의존성에 따라 바뀌므로 Pull Request의 실행 결과로만 보존한다.
