# TEST READY (테스트 환경 및 준비 가이드)

본 문서는 `laravel-administrator` 패키지의 E2E 통합 테스트를 정상적으로 구동하기 위한 환경 구축 및 준비 절차에 대해 기술합니다.

---

## 1. 테스트 준비 및 필수 구성 요건

통합 테스트 케이스는 호스트 어플리케이션(`sparekorea_admin`)에 로컬 심볼릭 링크 형태로 연동되어 기동됩니다. 원활한 동작을 위해 다음 조건이 미리 구축되어 있어야 합니다.

### 1) SQLite 및 PDO 드라이버 설치
테스트 데이터베이스 격리와 고속 실행을 위해 **SQLite 3** 및 PHP SQLite 확장 모듈(PDO SQLite)이 활성화되어 있어야 합니다.

### 2) 테스트 전용 SQLite 데이터베이스 파일 생성
인메모리 격리 외에, 설정 캐시(`config:cache`) 등 서브 프로세스 격리 동작 검증 시 영속적인 상태 유지를 위해 아래와 같이 테스트용 SQLite 파일을 생성해 두어야 합니다.
```bash
touch /Users/galahan/SaAkSin/artgrammer/sparekorea/web/admin/database/testing.sqlite
```

---

## 2. 데이터베이스 스키마 마이그레이션 사양

본 테스트 환경은 기존 호스트 프로젝트의 `tests/migrations` 내 마이그레이션 코드들을 수행함과 동시에 어드민 패키지의 기능과 완벽하게 매핑시키기 위해 **동적 테이블 구조 확장**을 `setUp` 과정에서 진행합니다.

### 1) 추가적인 테스트 테이블 구성
패키지 로딩 및 메뉴 빌딩 단계에서 예외(no such table)가 발생하는 것을 미연에 방지하기 위해 다음과 같은 껍데기(Skeleton) 테이블들을 동적으로 생성합니다.
- `spkorea_user_grades`: 회원 등급 정보 및 등급별 할인율 정보 저장
- `spkorea_orders`: 관계형 쿼리 및 통계 빌더 연동을 위한 주문 데이터 테이블 (`so_delivery_status`, `so_name`, `so_desc` 등 확장)
- `spkorea_forwarding_boxes`: 패키지 초기 구동 시 즉시 평가되는 전역 배치 액션 쿼리용 포워딩 박스 테이블

### 2) SQLite 호환용 PDO 사용자 정의 함수 등록
SQLite는 MySQL과 달리 문자열 결합을 수행하는 `CONCAT()` 내장 함수를 제공하지 않습니다. 패키지 컬럼 정의 내 `CONCAT` 쿼리의 호환성을 유지하기 위해 테스트 실행 중 sqlite 커넥션 인스턴스에 직접 PDO 함수를 등록합니다.
```php
$pdo->sqliteCreateFunction('CONCAT', function (...$args) {
    return implode('', $args);
});
```

---

## 3. 세션 및 미들웨어 조정 가이드

테스트 수행 중 권한 부족(302 Redirect) 및 토큰 불일치 문제를 우회하고 완벽한 세션 격리를 달성하기 위해 아래의 장치들을 적용합니다.

1. **CSRF 검증 미들웨어 비활성화**
   - 테스트용 API 요청 시 `VerifyCsrfToken` 미들웨어를 무시하도록 `withoutMiddleware`를 사용합니다.
2. **세션 드라이버 강제 고정**
   - 로컬 디스크 파일이나 데이터베이스 오염을 막기 위해 세션 드라이버를 `array`로 오버라이드합니다.
3. **설정 캐싱 하에서의 컨테이너 리프레시 (`refreshApplication`)**
   - 캐시 생성 명령어(`config:cache`) 수행 시 동일 PHP 프로세스의 컨테이너 바인딩이 리빌드되므로, 반드시 `$this->refreshApplication()`를 호출하여 Auth Guard 및 User Provider 등의 모든 싱글톤 컴포넌트들을 물리 디렉토리 캐시 파일을 기준으로 다시 정합성 있게 로드해주어야 합니다.

---

## 4. 테스트 구동 및 사후 관리

### 테스트 실행 전 환경 정리
기존 캐시 파일이 다른 테스트에 방해를 주지 않도록 테스트 실행 전후로 캐시를 자동으로 클리어하도록 설계되어 있습니다. 
만약 수동으로 설정 캐시를 클리어해야 하는 경우, 아래 명령을 수행하십시오.
```bash
php artisan config:clear
```
