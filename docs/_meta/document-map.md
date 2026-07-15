# 문서 지도

이 문서는 구현 영역과 갱신해야 할 문서 위치를 연결한다. 세부 페이지 목록보다 변경 시 함께 검토할 경계를 정의한다.

| 구현 영역 | 공개 매뉴얼 | 내부 Wiki | 주요 검증 |
| --- | --- | --- | --- |
| 설치·지원 버전·서비스 프로바이더 | `installation.md`, `ko/installation.md` | `wiki/compatibility/support-matrix.md` | Composer 검증, 관련 PHPUnit |
| 전역 설정·테마·커스텀 에셋 | `configuration.md`, `ko/configuration.md` | `wiki/architecture/theme-assets.md` | 관련 통합 테스트, Vite build |
| 마크다운·HTML 출력·관계 선택 UI | 관련 필드·컬럼 문서 | `wiki/security/rendering-boundaries.md` | 보안 입력 회귀, TypeScript 검사 |
| 빌드·테스트·문서 검증 | 기여 문서 | `wiki/development/verification.md` | 변경 경로별 명령 |
| VitePress 구조·탐색·게시 | 홈페이지와 매뉴얼 색인 | `docs/_meta/schema.md` | Arti Docs lint, VitePress build |

## 갱신 원칙

- 사용자 설정이나 공개 API가 바뀌면 공개 매뉴얼과 관련 내부 Wiki를 같은 Pull Request에서 검토한다.
- 내부 구현만 바뀌어 공개 사용법이 같다면 내부 Wiki만 갱신할 수 있다.
- 테스트 명령과 결과는 Pull Request body에 기록하고 Wiki에는 재사용 가능한 검증 기준만 남긴다.
- 외부 원문을 새로 보존하지 않는 작업은 `raw/`를 수정하지 않는다.
