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

## 결과 기록

Pull Request body에는 다음을 남긴다.

- 실행 명령과 종료 코드
- 관련 테스트를 선택한 근거
- 전체 테스트를 실행했다면 필요한 이유
- 미실행 검증과 환경 제약
- Wiki·raw 영향과 lint 결과

고정된 테스트 개수나 과거 실행 시간은 Wiki에 기록하지 않는다. 해당 값은 구현과 의존성에 따라 바뀌므로 Pull Request의 실행 결과로만 보존한다.
