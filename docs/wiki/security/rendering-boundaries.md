# 렌더링 보안 경계

관리자 UI는 일부 화면에서 Alpine의 `x-html`을 사용한다. 따라서 각 값이 HTML로 전달되기 전에 어느 계층에서 정규화·escape·sanitize되는지 유지해야 한다.

## 마크다운

[마크다운 초기화 코드](../../../resources/js/app.ts)는 `marked` renderer를 다음 정책으로 제한한다.

- 원시 HTML token은 HTML entity로 escape한다.
- 링크는 `http:`, `https:`, `mailto:`, `tel:`과 상대 경로만 허용한다.
- 이미지는 `http:`, `https:`와 상대 경로만 허용한다.
- URL 검사 전에 HTML entity, 제어문자와 공백을 정규화한다.
- `href`, `src`, `title`, `alt`에 들어가는 값은 attribute escape한다.
- 개발 환경에서는 위험 protocol과 attribute injection 입력을 DOM으로 파싱해 회귀 검사한다.

이 정책을 바꾸면 raw HTML, entity 우회, 제어문자 우회, `javascript:`, `data:`, `vbscript:`와 따옴표 주입을 함께 검증한다.

## 관계 선택 UI

[RelationSelectController](../../../resources/js/controllers/RelationSelectController.ts)는 옵션 문구와 검색어를 먼저 escape한 뒤 일치 부분에 제한된 강조 `<span>`만 추가한다. `highlight()` 반환값은 `x-html`로 표시되므로 escape 순서를 바꾸면 안 된다.

## 컬럼 HTML

[Column::renderOutput()](../../../src/SaAkSin/Administrator/DataTable/Columns/Column.php)은 일반 텍스트 컬럼을 기본적으로 escape한다. 사용자 정의 output, computed·related 컬럼처럼 HTML 호환 경로는 `clean()` helper가 있으면 이를 사용한다.

`clean()` helper가 없는 호스트에서는 HTML 호환 경로의 값이 그대로 반환될 수 있다. 따라서 신뢰할 수 없는 값을 HTML output으로 허용하지 않아야 하며, 이 fallback을 변경할 때는 기존 HTML 호환성과 Stored XSS 위험을 함께 검토해야 한다.

## 검수 체크

- 새로운 `x-html` 사용 지점에 데이터 출처와 sanitizer가 명확한가
- escape 후 허용된 markup만 추가하는가
- URL protocol과 attribute context를 각각 검증하는가
- 보안 테스트가 실제 DOM attribute를 확인하는가
- sanitizer가 없는 호스트 환경의 동작이 명시돼 있는가
