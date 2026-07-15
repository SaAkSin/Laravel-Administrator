# Laravel Administrator 13.0.0 릴리스 노트

> 상태: `13.0.0` 릴리스 게시를 승인했습니다. 이 문서가 포함된 `main` 커밋을 기준으로 태그, GitHub Release와 Packagist 패키지를 게시합니다.

## 변경 요약

- Laravel `^13.0`과 PHP `^8.3` 전용 지원 범위를 Composer 제약과 사용자 문서에 일치시켰습니다.
- Laravel 13에서 서비스 프로바이더와 설정 병합 시점이 정상 동작하도록 호환성을 보강했습니다.
- `silver` 기본 테마와 `legacy` 테마 선택, `custom_css`, `custom_js` 확장 설정을 추가했습니다.
- Vite가 앱과 `silver` 테마를 별도 엔트리로 빌드하며, 패키지의 `public/dist/.vite/manifest.json`에 두 엔트리가 포함됩니다.
- Composer 패키지 메타데이터와 lock을 릴리스 검증 가능한 상태로 정리했습니다.

## 업그레이드 참고사항

- 설치 환경은 Laravel `^13.0`과 PHP `^8.3`을 모두 충족해야 합니다.
- 기존 애플리케이션은 패키지를 설치하기 전에 이 런타임 요구사항을 충족하도록 업그레이드하십시오.
- 기존 화면 모양을 유지하려면 `administrator.theme`을 `legacy`로 지정하십시오.
- 기본값은 `silver`이며, 배포 전에 `npm run build`로 `public/dist`와 manifest를 함께 갱신해야 합니다.
- 사용자 정의 CSS는 테마 뒤에, 사용자 정의 JavaScript는 앱 엔트리 뒤에 로드됩니다.

## 릴리스 Architect 승인 체크리스트

- [x] 버전을 `13.0.0`으로 확정하고 Laravel `^13.0`, PHP `^8.3` 지원 범위를 검토한다.
- [x] PHP 8.3 실제 런타임에서 Composer 해석과 관련 PHPUnit 테스트를 검토한다.
- [x] `composer validate`, `composer audit`, Laravel 13 의존성 해석, 관련 PHPUnit 테스트와 `npm run build` 결과를 검토한다.
- [x] `public/dist/.vite/manifest.json`의 앱/실버 테마 엔트리와 산출물을 검토한다.
- [x] README, CHANGELOG와 본 릴리스 노트의 지원 범위가 일치하는지 검토한다.
- [x] `13.0.0` 태그 생성을 승인한다.
- [x] GitHub Release 게시를 승인한다.
- [x] Packagist 배포를 승인한다.

## 게시 대상

- Git 태그: `13.0.0`
- GitHub Release: `Laravel Administrator 13.0.0`
- Packagist: `saaksin/laravel-administrator` `13.0.0`
