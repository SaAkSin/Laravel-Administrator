# [PRD] laravel-administrator 프론트엔드 아키텍처 현대화 (Alpine.js + Tailwind CSS)

본 문서는 `laravel-administrator` 패키지의 레거시 프론트엔드 아키텍처인 Knockout.js 및 jQuery 환경을 현대적이고 가벼운 **Alpine.js**와 **Tailwind CSS** 체계로 점진적 전환하기 위한 제품 요구 사양서(PRD)입니다.

---

## 1. 문서 개요
- **제품명**: laravel-administrator (Laravel 관리자 페이지 패키지)
- **대상 기능**: 프론트엔드 데이터 바인딩, 그리드 렌더링, 폼 빌더 및 디자인 스타일링 전반
- **마이그레이션 타깃**: Knockout.js + jQuery + Vanilla CSS ➡️ **Alpine.js (v3) + Tailwind CSS (v3/v4) + Vite**

---

## 2. 배경 및 목적
현재 프론트엔드는 10여 년 전 기술인 Knockout.js에 의존하고 있어 다음과 같은 비즈니스 및 기술적 리스크가 누적되어 있습니다.
- **기술 고사**: Knockout.js의 커뮤니티가 완전히 단절되어 현대적 UI 컴포넌트와의 호환성이 매우 떨어지며 신규 개발자의 유지보수가 어렵습니다.
- **성능 저하**: 불필요한 jQuery 플러그인과 레거시 CSS 에셋들이 뭉쳐 있어 스크립트 용량이 비대하고 로딩 속도가 저하됩니다.
- **디자인 제약**: 반응형 스타일이 부재하고 구시대적인 UI 폼 렌더링으로 사용성 저하가 발생합니다.

**목표**: 기존 Laravel 컨트롤러의 비즈니스 로직과 데이터베이스 통신 스키마(API)는 **100% 보존**하면서, 프론트엔드 계층만 가볍고 현대적이며 미려한 구조로 전환하여 패키지 사용성과 가치를 극대화합니다.

---

## 3. 핵심 비즈니스 및 제품 목표
1. **성능 극대화 (Lightweight)**: 초기 프론트엔드 에셋 로딩 크기 60% 이상 절감 및 페이지 렌더링 INP(Interaction to Next Paint) 속도 2배 향상.
2. **현대적인 프리미엄 디자인**: 투박한 디자인을 지양하고, **Tailwind CSS** 기반의 반응형 디자인 시스템과 **다크 모드(Dark Mode)**를 기본 제공하여 프리미엄 관리 화면 제공.
3. **코드 현대화 및 고가독성**: 1,600줄에 달하는 전역 자바스크립트(`admin.js`)를 Alpine.js 컴포넌트 단위로 모듈화하여 유지보수성 200% 향상.

---

## 4. 핵심 기능 요구사항 (Functional Requirements)

### F-1. 그리드 뷰 및 데이터 연동 (Grid & Data Binding)
- **요구사항**: 
  - 백엔드 `/{model}/results` 엔드포인트와 연동되어 비동기로 데이터 테이블을 바인딩합니다.
  - Alpine.js의 `x-for`를 사용하여 반응형 및 미려한 데이터 테이블 그리드를 출력합니다.
  - 사용자가 특정 열 헤더를 클릭 시, 화면 리로드 없이 Alpine 상태 값을 통해 실시간 정렬(`sortOptions`) 및 페이징이 수행되어야 합니다.

### F-2. 검색 필터 및 퀵 서치 (Filters)
- **요구사항**:
  - 사이드바의 조건부 필터 영역을 Alpine.js 기반 컴포넌트로 렌더링합니다.
  - 텍스트, 숫자 범위, 날짜 범위, 셀렉트 박스 등 다양한 필터 타입의 양방향 바인딩(`x-model`)이 매끄럽게 연동되어야 합니다.

### F-3. 동적 폼 빌더 (Dynamic Form Builder)
- **요구사항**:
  - 특정 레코드를 클릭할 때 활성화되는 아이템 수정/등록 폼을 Alpine.js 기반 템플릿(`x-html` 또는 `template`)으로 바인딩합니다.
  - 일대다(HasMany), 다대다(BelongsToMany) 등의 관계형 드롭다운(`Select2` 대체 컴포넌트)을 Alpine과 연동하여 자동완성 검색 기능과 연동합니다.
  - 저장(`saveItem`), 삭제(`deleteItem`) 시 스피너 UI와 연동된 부드러운 전환 효과(Transition)가 나타나야 합니다.

### F-4. 스타일 시스템 전면 개편 (Tailwind CSS)
- **요구사항**:
  - 복잡한 가변 픽셀 연산 코드(`$formWidth` 관련 인라인 스타일)를 Tailwind의 CSS Grid, Flexbox, Container Queries 기반 스타일로 대체하여 완전한 모바일/태블릿 반응형 UI를 제공합니다.
  - 세련된 HSL 기반의 슬레이트(Slate)/인디고(Indigo) 컬러 팔레트와 부드러운 Glassmorphism 디자인, 마이크로 인터랙션을 반영합니다.

---

## 5. 상세 기술 아키텍처 및 마이그레이션 설계

### A. 기술 스택 매핑
- **AS-IS (Legacy)**: Knockout.js v3 + jQuery + Vanilla CSS + Plupload / Select2 / CKEditor
- **TO-BE (Modern)**: Alpine.js v3 + Fetch API + Tailwind CSS + Vite + Modern Light Components

### B. 주요 구문 일대일 변환 사양 (Syntax Mapping)
개발자가 안전하게 뷰 코드를 포팅할 수 있도록 문법 매핑 테이블을 준수합니다.

| 기능 | Knockout.js (AS-IS) | Alpine.js (TO-BE) | 비고 |
| :--- | :--- | :--- | :--- |
| **컨텍스트 정의** | `data-bind="with: viewModel"` | `x-data="adminController()"` | 컨트롤러 정의 |
| **양방향 입력 바인딩** | `data-bind="value: field_value"` | `x-model="field_value"` | 입력 필드 모델 연동 |
| **조건부 렌더링** | `data-bind="visible: loading"` | `x-show="loading"` 또는 `x-if` | UI 스피너 등 제어 |
| **텍스트 바인딩** | `data-bind="text: modelTitle"` | `x-text="modelTitle"` | 타이틀 출력 |
| **루프 처리** | `data-bind="foreach: rows"` | `template x-for="row in rows"` | 데이터 테이블 각 행 출력 |
| **이벤트 처리** | `data-bind="click: saveItem"` | `@click="saveItem()"` | 저장 버튼 핸들러 |

---

## 6. 단계별 마이그레이션 로드맵 (Milestones)

### [Phase 1] 개발 환경 및 빌드 인프라 셋업 (1주차)
* **목표**: 현대화된 자바스크립트 및 CSS 빌드 환경 도입
* **주요 작업**:
  - 패키지 내 `package.json` 신설 및 Vite 번들러 연동
  - Tailwind CSS 컴파일 환경 구축
  - `views/layouts/default.blade.php`에 로드할 Vite 디렉티브(`@vite`) 또는 빌드된 모던 에셋 리스트 등록부 구축

### [Phase 2] API 연동 레이어 표준화 (1~2주차)
* **목표**: jQuery 비동기 통신을 Fetch API 체계로 전면 대체
* **주요 작업**:
  - `admin.js` 내의 모든 `$.ajax` 호출을 `fetch` 또는 `Axios` 기반 비동기 함수로 포팅
  - 글로벌 `adminData` JSON 데이터를 읽어서 Alpine.js 상태 구조로 매핑하는 부트스트랩 모듈 작성

### [Phase 3] 뷰 템플릿 디렉티브 치환 (2~3주차)
* **목표**: HTML 블레이드 파일 내 모든 Knockout 구문 제거
* **주요 작업**:
  - `views/templates/admin.php` ➡️ Alpine.js 기반 테이블 및 페이징 마크업 포팅
  - `views/templates/edit.php` ➡️ 동적 폼 빌더 입력 컨트롤들의 Alpine 디렉티브 포팅
  - `views/templates/filters.php` ➡️ 사이드바 필터 바인딩 치환

### [Phase 4] 서드파티 레거시 라이브러리 경량화 (3~4주차)
* **목표**: jQuery 의존성 라이브러리를 가벼운 현대 라이브러리로 대체
* **주요 작업**:
  - Select2 ➡️ Alpine.js 기반 경량 커스텀 Combobox 컴포넌트로 대체
  - Plupload ➡️ HTML5 Dropzone 및 바닐라 JS 멀티 업로더 연동
  - CKEditor ➡️ 무료 CKEditor 4 복원 (로컬 번들 및 Full 스펙 제공, `wysiwyg` 타입) 및 Quill 에디터 분리 탑재 (`wysiwyg2` 타입) 이원화 표준화

### [Phase 5] 통합 테스트 및 미세 최적화 (4주차)
* **목표**: 100% 기능 정합성 보장 및 릴리즈 준비
* **주요 작업**:
  - 기존 백엔드 단위 테스트(276개)가 완전 통과하는 상태에서, 타깃 Laravel 환경과 심볼릭 링크 연동을 통한 브라우저 동작 전수 검증
  - 모바일 및 태블릿 뷰에서 반응형 디자인 레이아웃 깨짐 검사 및 다크 모드 완성도 검증

---

## 7. 성공 지표 및 검증 기준 (UAT & Quality Metrics)

### A. 성능 및 품질 기준 (Performance Metrics)
- **에셋 번들 크기**: 자바스크립트 및 CSS를 포함한 총 프론트엔드 에셋 크기가 **300KB 미만**일 것 (기존 대비 60% 절감 목표).
- **INP (Interaction to Next Paint)**: 버튼 클릭 및 페이지 페이징 동작 시 화면 피드백 지연 속도가 **100ms 이내**로 쾌적할 것.
- **반응형 보장**: Chrome DevTools 상의 320px 모바일 화면부터 1920px 데스크톱 화면까지 가로 스크롤 없이 완전 반응형 그리드가 표현될 것.

### B. 최종 인수 조건 (Acceptance Criteria)
1. 사용자가 타깃 Laravel 프로젝트에서 패키지 에셋을 퍼블리시하여 실행했을 때, 관리자 첫 화면에서 데이터 목록이 에러 없이 출력된다.
2. 필터를 추가하고 적용했을 때 비동기 통신을 통해 화면 리로드 없이 실시간으로 리스트가 필터링된다.
3. 임의의 컬럼 헤더 클릭 시 정상적으로 오름차순/내림차순 정렬 쿼리가 동작하여 갱신된다.
4. 신규 등록 및 기존 항목 수정 폼이 세련된 모던 CSS 스타일로 렌더링되며, 저장을 눌렀을 때 백엔드에 바인딩된 Eloquent 모델이 오류 없이 저장 및 업데이트된다.
5. `wysiwyg` 타입 호출 시 로컬 번들 CKEditor 4 (Full Spec)가 정상 렌더링되고, `wysiwyg2` 타입 호출 시 Quill 에디터가 정상 렌더링되며 둘 다 Alpine.js 바인딩과 양방향 싱크가 유지된다.
6. `php artisan vendor:publish --tag=laravel-administrator` 전용 퍼블리시 태그를 실행했을 때 에셋이 정상 복사된다.
