# 지원 버전과 호환성

## 패키지 제약

[Composer 설정](../../../composer.json)의 현재 지원 범위는 다음과 같다.

| 영역 | 제약 |
| --- | --- |
| PHP | `^8.3` |
| Laravel | `^13.0` |
| Orchestra Testbench | `^11.0` |
| PHPUnit | `^11.0` |
| Laravel Octane | 호스트가 선택적으로 설치하는 Laravel 13 호환 2.x |

`config.platform.php`는 최저 지원 런타임인 `8.3.0`으로 고정한다. `minimum-stability`는 `dev`이고 `prefer-stable`은 활성화돼 있다. Composer 제약과 lockfile은 설치 가능 범위를 나타내지만 실제 런타임 테스트를 대신하지 않는다.

## Octane 요청 생명주기

- 패키지 운영 `require`에는 `laravel/octane`을 추가하지 않는다. RoadRunner, Swoole, FrankenPHP와 Octane 설치·배포는 호스트 애플리케이션의 책임이다.
- `admin_validator`, `admin_config_factory`, `itemconfig`, 필드·컬럼·액션 factory, data table과 menu는 Laravel scoped binding으로 등록한다. 같은 요청·작업 안에서는 인스턴스를 재사용하고 다음 lifecycle에서는 새 인스턴스를 만든다.
- 모델·설정 `itemconfig`는 현재 라우트 parameter로 해석한다. dashboard, custom page와 secure asset 경로에서는 해석하지 않는다.
- 관리자 validator는 공유 validation factory를 clone한 전용 factory에서 만든다. 호스트가 등록한 resolver, 확장, presence verifier와 컨테이너 계약을 공유 factory에서 변경하지 않는다.
- 관리자 로케일은 `web` 세션 이후 요청 미들웨어에서 매번 적용한다. 세션 값이 없거나 허용되지 않으면 애플리케이션 기본 로케일을 사용한다.
- `administrator.ready`는 애플리케이션 또는 워커 부트 시 한 번 발생하며 요청별 초기화 지점이 아니다.
- 코드나 설정 배포 뒤 실행 중인 워커에는 `php artisan octane:reload`를 수행한다.

## 호환성 검증 원칙

- 지원 범위를 바꾸면 `composer.json`, 공개 설치 문서와 이 페이지를 같은 Pull Request에서 갱신한다.
- PHP 8.3에서 Composer 해석 결과와 관련 Testbench 테스트를 기록한다.
- 로컬 PHP 버전 때문에 직접 검증하지 못하면 명령, 실패 원인과 후속 확인 방법을 Pull Request body에 남긴다.
- 보안 advisory로 설치가 차단된 결과를 성공으로 기록하지 않는다.

사용자 설치 절차는 [한국어 설치 매뉴얼](../../ko/installation.md)과 [영문 설치 매뉴얼](../../installation.md)을 따른다.
