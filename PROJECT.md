# Project: laravel-administrator Refactoring & Modernization

## Architecture
- laravel-administrator 패키지는 라라벨의 관리자 페이지를 동적으로 구성해주는 패키지입니다.
- 이번 리팩토링에서는 (1) 설정 캐싱(Serialization)을 위해 클로저를 제거하고 콜백을 리팩토링하고, (2) 조인 쿼리를 Eloquent Subquery로 전환하여 SoftDeletes 우회를 방지하며, (3) Vite HMR 및 안전 에셋 로더를 구현합니다.

## Code Layout
- 패키지 루트: `/Users/galahan/SaAkSin/artgrammer/laravel-administrator`
  - 소스 디렉토리: `src/SaAkSin/Administrator`
  - 뷰 템플릿: `src/views`
  - 라우트 설정: `src/routes.php`
  - 설정 구성 및 밸리데이터: `src/SaAkSin/Administrator/Config/`, `src/SaAkSin/Administrator/Validator.php`
  - 미들웨어: `src/SaAkSin/Administrator/Http/Middleware`
- 호스트 프로젝트: `/Users/galahan/SaAkSin/artgrammer/sparekorea/web/admin`
  - 설정 파일: `config/administrator.php`
  - 테스트 및 구현 연동 지점

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | R1: Closure Refactoring | config/administrator.php 내 클로저 제거, Validator 완화, app()->call() 적용 | none | DONE |
| 2 | R2: Eloquent API Subquery | filterQuery 시그니처 수정, Eloquent subQuery & selectSub() 적용, array_splice 제거 | none | DONE |
| 3 | R3: Vite HMR & Asset Loader | hot 파일 감지, @vite/client 연동, serveAsset 액션/라우트 추가 및 에셋 주입 링크 대체 | none | DONE |
| 4 | R4: E2E Integration Testing | R1~R3 구현 완료 후 E2E 테스트 검증 | M1, M2, M3 | DONE |
| 5 | R5: Editor Separation & UI Refinements | 무료 CKEditor 4 (wysiwyg) 및 Quill (wysiwyg2) 이원화, 이미지 업로드/삭제 UI 및 관계형 콤보박스 정교화, 패키지 전용 퍼블리시 태그 추가 | none | DONE |
| 6 | R6: Combobox Clear Button Fix | 콤보박스 초기 빈 상태일 때 삭제 아이콘 은폐 및 클릭 시 초기화 동작 보장, 에셋 빌드/배포 | none | DONE |

## Interface Contracts
### Config/Validator ↔ Middleware
- `permission` 설정값은 문자열, 배열, 또는 callable을 허용하며, `ValidateAdmin` 미들웨어는 `app()->call()`을 통해 실행하고 유효성을 평가함.
- Validator는 `string_or_callable` 규칙을 통해 `permission`을 검증함.

### DataTable ↔ Relationships
- `DataTable.php`에서 `$column->filterQuery($query, $selects)` 형태로 메인 Eloquent 쿼리 객체 `$query`를 주입함.
- 각 관계형 컬럼(`BelongsTo`, `HasOneOrMany`, `BelongsToMany`)은 메인 쿼리에 `$query->selectSub($subQuery, $columnName)` 형식으로 서브쿼리를 결합함.
