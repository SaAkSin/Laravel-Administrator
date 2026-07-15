# Laravel Administrator 13.0.1 릴리스 노트

> 상태: `13.0.1` 릴리스 게시를 승인했습니다. 이 문서가 포함된 `main` 커밋을 기준으로 태그, GitHub Release와 Packagist 패키지를 게시합니다.

## 변경 요약

- 요청 상태를 보관하는 관리자 서비스와 `itemconfig`를 Laravel scoped binding으로 전환했습니다.
- Laravel Octane Worker가 서로 다른 모델·사용자·세션 요청을 연속 처리해도 권한, 설정, 필드·컬럼·액션, 필터, 페이지당 행 수와 로케일 상태가 공유되지 않도록 격리했습니다.
- 공유 validation factory를 변경하지 않는 관리자 validator를 사용해 호스트 애플리케이션의 사용자 정의 resolver와 확장을 보존합니다.
- Laravel Octane `^2.0`은 통합 테스트용 개발 의존성에만 추가하며 운영 설치를 강제하지 않습니다.
- README와 영문·한국어 설치·다국어 문서, 내부 지원 매트릭스와 검증 Wiki에 Octane 지원 범위와 운영 절차를 기록했습니다.

## 업그레이드 참고사항

- 설치 환경은 Laravel `^13.0`과 PHP `^8.3`을 모두 충족해야 합니다.
- PHP-FPM 환경은 별도 설정 변경 없이 계속 지원합니다.
- Octane은 호스트 애플리케이션이 선택적으로 설치하며 이 패키지의 운영 의존성이 아닙니다.
- RoadRunner, Swoole 또는 FrankenPHP 기반 Octane 환경에 배포한 뒤에는 `php artisan octane:reload`로 워커를 다시 불러오십시오.
- `administrator.ready`는 애플리케이션 또는 워커 부트 시 한 번 발생하며 요청별 상태 초기화 이벤트가 아닙니다.

## 게시 전 검증 결과

- `composer validate --strict`: exit code 0, `composer.json` valid
- `composer audit --locked`: exit code 0, 보안 취약점 advisory 없음
- `./vendor/bin/phpunit --display-deprecations`: exit code 0, 301 tests, 347 assertions
- `npm run docs:build`: exit code 0, VitePress build 성공
- PHP 8.4 implicit nullable deprecation 1건은 기존 `Config/Model/Config.php:307` 경고이며 13.0.1의 Octane 지원 범위와 무관합니다.

## 릴리스 Architect 승인 체크리스트

- [x] 버전을 `13.0.1`로 확정하고 Laravel `^13.0`, PHP `^8.3`, 선택적 Octane `^2.0` 지원 범위를 검토한다.
- [x] 공식 Octane Worker 연속 요청 테스트와 scoped lifecycle·필터·권한·로케일·validation 회귀 테스트를 검토한다.
- [x] `composer validate --strict`, `composer audit`, 전체 PHPUnit과 문서 build 결과를 검토한다.
- [x] README, CHANGELOG, 공개 문서, 내부 Wiki와 본 릴리스 노트의 지원 범위가 일치하는지 검토한다.
- [x] PHP-FPM 환경이 별도 Octane 설정 없이 계속 동작하는지 구현과 전체 회귀 테스트로 검토한다.
- [x] `13.0.1` 태그 생성을 승인한다.
- [x] GitHub Release 게시를 승인한다.
- [x] Packagist 배포를 승인한다.

## 게시 대상

- Git 태그: `13.0.1`
- GitHub Release: `Laravel Administrator 13.0.1`
- Packagist: `saaksin/laravel-administrator` `13.0.1`
