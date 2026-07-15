# Laravel Administrator 10.8.0 릴리스 노트 초안

> 상태: 릴리스 후보. 최종 태그명, GitHub Release 게시 시점과 Packagist 배포 시점은 미정입니다.

## 변경 요약

- Laravel 10, 11, 12, 13 지원 범위를 Composer 제약과 사용자 문서에 일치시켰습니다.
- Laravel 13에서 서비스 프로바이더와 설정 병합 시점이 정상 동작하도록 호환성을 보강했습니다.
- `silver` 기본 테마와 `legacy` 테마 선택, `custom_css`, `custom_js` 확장 설정을 추가했습니다.
- Vite가 앱과 `silver` 테마를 별도 엔트리로 빌드하며, 패키지의 `public/dist/.vite/manifest.json`에 두 엔트리가 포함됩니다.
- Composer 패키지 메타데이터와 lock을 릴리스 검증 가능한 상태로 정리했습니다.

## 업그레이드 참고사항

| Laravel | 최소 PHP | 권장 테스트 조합 |
| --- | --- | --- |
| 10.x | 8.1 | Testbench 8 / PHPUnit 9.6 |
| 11.x | 8.2 | Testbench 9 / PHPUnit 10.5 |
| 12.x | 8.2 | Testbench 10 / PHPUnit 11 |
| 13.x | 8.3 | Testbench 11 / PHPUnit 11 |

- 기존 화면 모양을 유지하려면 `administrator.theme`을 `legacy`로 지정하십시오.
- 기본값은 `silver`이며, 배포 전에 `npm run build`로 `public/dist`와 manifest를 함께 갱신해야 합니다.
- 사용자 정의 CSS는 테마 뒤에, 사용자 정의 JavaScript는 앱 엔트리 뒤에 로드됩니다.

## 알려진 제한사항

- Laravel 주 버전별 검증은 현재 실행 환경의 PHP 버전에서 Composer 의존성 해석으로 수행합니다. 실제 최소 PHP 버전별 런타임 검증은 배포 파이프라인의 버전 매트릭스가 필요합니다.
- Composer의 기본 보안 차단 정책에서 Laravel 10·11 조합은 공개된 최신 후보까지 advisory 영향을 받아 설치 해석이 거부됩니다. 의존성 그래프는 진단 전용 `--no-security-blocking` 옵션에서 해석되지만, 이 옵션을 운영 설치에 적용해서는 안 됩니다.
- Laravel 12·13 조합은 Composer 기본 보안 정책을 유지한 dry-run에서 정상 해석됐습니다.
- 최종 태그명은 아직 확정되지 않았으며, 이 문서의 `10.8.0`은 릴리스 후보 버전입니다.
- GitHub Release와 Packagist에는 아직 게시하지 않습니다.

## 릴리스 전 Architect 승인 체크리스트

- [ ] 최종 태그명을 확정한다.
- [ ] Laravel 10~13 및 PHP 버전 매트릭스 결과를 검토한다.
- [ ] Laravel 10·11에 대한 Composer advisory 차단이 해소됐거나 별도 지원 정책이 승인됐는지 확인한다.
- [ ] `composer validate`, 관련 PHPUnit 테스트와 `npm run build` 결과를 검토한다.
- [ ] `public/dist/.vite/manifest.json`의 앱/실버 테마 엔트리와 산출물을 검토한다.
- [ ] README, CHANGELOG와 본 릴리스 노트의 지원 범위가 일치하는지 검토한다.
- [ ] GitHub Release 게시 시점을 승인한다.
- [ ] Packagist 배포 시점을 승인한다.

## 이번 PR에서 수행하지 않는 작업

- Git 태그 생성
- GitHub Release 게시
- Packagist 배포
