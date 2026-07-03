## Changelog

### 10.7.0
- **공식 문서 사이트 및 매뉴얼 최신화**:
  - VitePress 기반 문서 사이트 구성을 정비하고 `administrator.artgrammer.co.kr`에서 공개 매뉴얼을 제공할 수 있도록 문서 구조와 내비게이션 정리
  - 영문 매뉴얼의 좌측 메뉴, 마크다운 제목, 코드 블록 언어 지정 및 하이라이트 표시 기준 보완
- **마크다운 보안 강화**:
  - 링크와 이미지 렌더링 시 `javascript:`, `data:`, `vbscript:` 등 위험 프로토콜을 차단하여 XSS 방어 강화
  - DOMParser 기반 회귀 테스트를 추가하여 마크다운 렌더링 보안 동작 검증
- **레거시 정리**:
  - 더 이상 Knockout.js에 종속되지 않는 번역 파일명을 `knockout.php`에서 `frontend.php`로 변경
  - 사용되지 않거나 혼선을 주는 레거시 프론트엔드 잔재와 문서 표현 정리

### 10.6.0
- **하위 디렉토리 배포(Subfolder Deployment) 공식 지원**:
  - Vite 에셋 빌드 base 경로 설정을 상대 경로(`./`)로 개선하여 호스트 프로젝트가 도메인 하위 경로에 배포되더라도 웹 폰트(Oxygen) 및 이미지 등의 에셋 404 로드 실패 버그 해결
- **모바일 반응형 UI/UX 대대적 보완**:
  - 모바일 해상도에서 상단 헤더 메뉴(`#menu_button`)와 필터(`#filter_button`)의 토글 상태 및 상호 배타성 제어 메커니즘 완성
  - 다단계 아코디언 메뉴 정상 개폐 지원 및 CSS 가중치 계산 조정을 통해 인라인 `!important` 배제
  - 모바일 뷰포트에서 데이터 테이블 열이 임의 탈락되는 로직을 제거하고 가로 스크롤 방식(`overflow-x: auto`)으로 데이터 유지
  - 모바일 상세 폼(`item_edit`) 가로 너비 확장을 위해 좌측 패딩 조정 (`27px` -> `8px`)
- **관계형 콤보박스(Select) 반응성 및 키보드 네비게이션 개선**:
  - 검색 목록 탐색을 위한 키보드 방향키(`ArrowUp`/`ArrowDown`) 및 선택(`Enter`) 키 기능 지원
  - 선택값이 플레이스홀더 상태일 때 단일 선택 삭제 버튼이 오작동하여 지속적으로 노출되던 Alpine.js 조건 정합성 수정
- **설정 파일 캐싱(config:cache) 런타임 오류 방지 리팩토링**:
  - 설정 로드 단계에서 클로저/글로벌 헬퍼 함수 의존성을 배제하고 순수 PHP 배열 반환 및 도메인 모델 내 정적 메소드로 캡슐화

### 10.5.2
- **이미지 미리보기 렌더링 기준 변경**:
  - 이미지(`type => 'image'`) 필드의 썸네일/미리보기 표시 시 세로 기준(`max-height: 100px`)이 아닌 가로 기준(`max-width: 100px; height: auto`)으로 렌더링하도록 변경
  - 반응형 모바일 미디어 쿼리 내 이미지 max-height 제한을 제거하여 가로 비율을 우선하도록 스타일 교정

### 10.5.1
- **WYSIWYG 에디터 이원화 탑재 및 UI 정밀 개선**:
  - 기존 `type => 'wysiwyg'`에 무료 CKEditor 4 복원 (Full 스펙 툴바 및 로컬 번들 제공)
  - `type => 'wysiwyg2'`로 Quill 에디터 분리 탑재하여 유연한 에디터 구성 지원
  - 에셋 배포를 위한 전용 태그 `laravel-administrator` 추가 (`php artisan vendor:publish --tag=laravel-administrator`)
  - 관계형 Combobox UI 정교화 (가로 너비 일치, 초기화 버튼 입체 버튼 오염 수정 및 수직 구분선 제거)
  - 이미지 업로드 필드 UI 개선 (Upload Image 버튼에 사진 SVG 아이콘 탑재 및 vertical-align 정렬 일치)
  - 이미지 삭제 버튼 UI 개선 (휴지통 SVG 아이콘 적용, 이미지 배치 영역의 최우측 상단에 콤팩트한 사각 라운드 형태로 절대 배치 고정, 내부 아이콘 정중앙 정렬)

### 10.5.0
- **프론트엔드 아키텍처 현대화 (Vite + Alpine.js + Tailwind CSS) 전면 개편**:
  - 기존 레거시 `Knockout.js` 및 `jQuery` 기반 아키텍처 완전 제거
  - Alpine.js v3 기반 반응형 데이터 바인딩 및 상태 관리 도입
  - Tailwind CSS 기반 모던 UI 시스템 구축 (반응형 레이아웃 및 다크 모드 기본 지원)
  - Vite 에셋 번들러 환경 도입 및 초기 로딩 성능 최적화 (에셋 크기 60% 이상 절감)
- **서드파티 라이브러리 경량화**:
  - `Select2` 의존성 제거 및 Alpine.js 기반 경량 커스텀 Combobox(자동완성) 적용
  - `Plupload` 제거 및 HTML5 바닐라 멀티 업로더 적용
  - 레거시 에디터 대체용 `Quill` WYSIWYG 및 `Marked` 마크다운 컴포넌트 탑재

### 10.0.0
- **라라벨 10.x 및 PHP 8.1+ 공식 지원**

### 5.8.1

- Added: View 모델 지원(alpha), MySQL InnoDB desc 정렬 속도 이슈 대응

### 5.8.0

- 라라벨 5.8.0 지원
- Added: FULL TEXT 검색(MySQL), https 지원
