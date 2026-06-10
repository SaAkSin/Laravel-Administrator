## Changelog

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
