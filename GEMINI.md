# 🤖 GEMINI.md — Laravel-Administrator 패키지 개발 명세 및 가이드라인

본 문서는 **Laravel-Administrator** 패키지 프로젝트의 핵심 아키텍처, 작업 이력, 컴파일 및 빌드 가이드라인을 수록하고 있습니다. 후속 AI 개발 에이전트(Gemini 등)가 본 패키지를 안전하고 신속하게 유지보수 및 연동 개발할 수 있도록 돕는 지침서입니다.

---

## 📂 1. 프로젝트 및 연동 레이아웃

- **로컬 패키지 루트:** `/Users/galahan/SaAkSin/artgrammer/laravel-administrator` (본 디렉토리)
- **연동 호스트 프로젝트 루트 (예시):** `../sparekorea/web/admin` (상대경로 기준)
- **패키지 빌드 대상:** 컴파일된 JS/CSS 번들은 `public/dist/` 경로로 출력됩니다.
- **에이전트 히스토리 문서 디렉토리:** `.agents/`
  - 에이전트가 패키지 분석 및 테스트 세팅 과정에서 수립한 문서들(`PRD.md`, `DEVELOPMENT_GUIDE.md`, `TEST_INFRA.md`, `ADOPTION_ANALYSIS.md`, `review.md` 등)을 한데 모아 정리했습니다.

---

## 🛠️ 2. 핵심 작업 완료 내역 (Milestones)

### 2.1. 에디터 이원화 (CKEditor 4 & Quill)
- `type => 'wysiwyg'` 설정 시 로컬 Full Spec **CKEditor 4**가 매핑되고, `type => 'wysiwyg2'` 설정 시 경량 **Quill** 에디터가 독립 구동되도록 이원화 설계했습니다.
- 두 에디터 모두 Alpine.js와 양방향 데이터 싱크가 정합성을 유지하도록 바인딩 처리했습니다.

### 2.2. Eloquent API 전환 (SoftDeletes 호환)
- 기존 Raw SQL Join 조립 방식을 Eloquent Builder의 `selectSub` API 조합(`toSql()`, `getBindings()`)으로 재작성하여, 회원이나 부품 등의 논리 삭제(`SoftDeletes`) 데이터가 어드민 관리 화면에 완벽하게 필터링(노출 안 됨)되도록 안정성을 확보했습니다.

### 2.3. 콤보박스(Combobox) UI 반응성 개선
- 초기 비어있거나 `-- 검색 또는 선택 --` 플레이스홀더 상태일 때 X 삭제 버튼(`×` 아이콘)이 숨겨지지 않던 버그를, 원본 CSS 내 `.clear-btn` 스타일의 `display: inline-block !important;`를 제거함으로써 Alpine.js `x-show` 지시어의 런타임 제어권이 온전히 회복되도록 해결했습니다.
- 자동완성 검색 상태에서 키보드 방향키 위/아래(`ArrowUp`, `ArrowDown`)를 통해 포커스 활성화가 이동하고 엔터(`Enter`) 키 입력 시 올바르게 선택되는 키보드 네비게이션을 완성했습니다.

### 2.4. 글로벌 함수 래핑 설정의 OOP화 및 마이그레이션
- 기존의 글로벌 헬퍼 함수(예: `spkorea_orders()`, `spkorea_qnas()`)를 선언하여 호출하는 레거시 방식을, 패키지 설정 로더의 유연한 확장을 바탕으로 순수 PHP 배열(`return [ ... ];`) 구조로 마이그레이션했습니다.
- 이를 통해 라라벨 설정 캐싱(`php artisan config:cache`) 수행 시 글로벌 함수 중복 선언 혹은 미선언(call_user_func 오류) 등으로 인한 런타임 중단 현상을 100% 원천 예방했습니다.
- 설정 파일 내에서 사용되던 금액 포맷팅 글로벌 헬퍼 함수(`formatOrderAmount()`)를 `SpkoreaOrder::formatAmount()` 모델 정적 메소드로 캡슐화 이관하여 결합도를 낮추고 도메인 모델 중심의 OOP 아키텍처를 실현했습니다.

---

## ⚙️ 3. 개발 및 컴파일 명령어 레퍼런스

### 3.1. 에셋 빌드 및 호스트 프로젝트 동기화 (rsync)
패키지에서 JS/CSS 소스코드를 변경한 경우, 아래 명령어 블록을 순차 실행하여 호스트 프로젝트에 반영해 주어야 합니다.
```bash
# 1. 패키지 에셋 프로덕션 빌드
npm run build

# 2. 빌드된 에셋 및 PHP 소스코드 호스트 프로젝트로 동기화 (호스트 경로에 맞춤 수정 필요)
rsync -av --delete public/dist/ ../sparekorea/web/admin/public/packages/saaksin/administrator/dist/
rsync -av --delete public/dist/ ../sparekorea/web/admin/vendor/saaksin/laravel-administrator/public/dist/
rsync -av src/ ../sparekorea/web/admin/vendor/saaksin/laravel-administrator/src/
```

---

## ⚠️ 4. 향후 작업 이행을 위한 불변의 규칙 (Cautions)

1. **[🚨 매우 중요 - PHPUnit 테스트 시 실제 DB Wipe 방지 규칙]**
   - 패키지의 연동 통합 테스트 코드(`tests/Feature/AdministratorIntegrationTest.php` 등)를 구동할 때, 호스트 프로젝트 `.env` 파일의 실제 개발용 데이터베이스 접속 정보(`DB_PASSWORD` 주석 해제 상태)가 로드되어 있으면 `parent::setUp()` 단계에서 **실제 데이터베이스에 `migrate:fresh`를 날려 모든 테이블이 영구 유실(Wipe)되는 현상**이 발생합니다.
   - **재발 방지 프로세스**:
     1. 테스트 구동 전, 반드시 호스트 및 패키지 환경에서 `php artisan config:clear` 명령을 선행 실행하여 캐시 설정의 간섭을 완전히 배제해야 합니다.
     2. 로컬 테스트 실행 시 `.env` 설정의 실제 DB 비밀번호는 안전하게 주석 처리하거나, 외부 DB 접근이 불가능한 전용 격리 환경에서만 유닛 테스트를 구동하도록 강제합니다.
2. **CSS `!important` 지양 규칙**
   - UI 마크업에 `!important`를 사용하는 경우 Alpine.js 반응성 인라인 스타일 오염으로 인해 요소가 은폐되지 않는 등의 UI 버그가 재발할 수 있으므로, CSS 선택자 우선순위 점수를 정교히 계산하여 코딩해야 합니다.
3. **한국어 작성 의무**
   - 커밋 메시지, 소스코드 주석 및 아티팩트 보고서는 **반드시 한국어**로 정성스럽게 기술되어야 합니다.
