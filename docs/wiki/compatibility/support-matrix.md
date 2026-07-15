# 지원 버전과 호환성

## 패키지 제약

[Composer 설정](../../../composer.json)의 현재 지원 범위는 다음과 같다.

| 영역 | 제약 |
| --- | --- |
| PHP | `^8.3` |
| Laravel | `^13.0` |
| Orchestra Testbench | `^11.0` |
| PHPUnit | `^11.0` |

`config.platform.php`는 최저 지원 런타임인 `8.3.0`으로 고정한다. `minimum-stability`는 `dev`이고 `prefer-stable`은 활성화돼 있다. Composer 제약과 lockfile은 설치 가능 범위를 나타내지만 실제 런타임 테스트를 대신하지 않는다.

## 호환성 검증 원칙

- 지원 범위를 바꾸면 `composer.json`, 공개 설치 문서와 이 페이지를 같은 Pull Request에서 갱신한다.
- PHP 8.3에서 Composer 해석 결과와 관련 Testbench 테스트를 기록한다.
- 로컬 PHP 버전 때문에 직접 검증하지 못하면 명령, 실패 원인과 후속 확인 방법을 Pull Request body에 남긴다.
- 보안 advisory로 설치가 차단된 결과를 성공으로 기록하지 않는다.

사용자 설치 절차는 [한국어 설치 매뉴얼](../../ko/installation.md)과 [영문 설치 매뉴얼](../../installation.md)을 따른다.
