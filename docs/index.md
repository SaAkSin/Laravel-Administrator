---
layout: home

hero:
  name: Laravel Administrator
  text: Laravel 10 관리자 페이지 빌더
  tagline: Eloquent 모델, 관계형 데이터, 설정 페이지를 선언형 구성으로 빠르게 관리합니다.
  image:
    src: /favicon.svg
    alt: Laravel Administrator
  actions:
    - theme: brand
      text: 설치 시작
      link: /docs/ko/installation
    - theme: alt
      text: 매뉴얼 보기
      link: /docs/ko/documentation
    - theme: alt
      text: GitHub
      link: https://github.com/SaAkSin/Laravel-Administrator

features:
  - title: Laravel 10 지원
    details: PHP 8.1 이상과 Laravel 10 기반 프로젝트에서 관리자 화면을 구성합니다.
  - title: 선언형 관리자 구성
    details: 모델, 컬럼, 필드, 필터, 액션을 PHP 설정 파일로 정의합니다.
  - title: 현대화된 프론트엔드
    details: Vite, Alpine.js, Tailwind CSS 기반으로 레거시 의존성을 줄였습니다.
  - title: 관계형 데이터 관리
    details: belongsTo, belongsToMany 관계형 필드와 컬럼을 관리자 UI에서 다룹니다.
  - title: 설정 페이지
    details: 모델과 별개로 사이트 설정, 캐시 처리, 운영 액션 페이지를 구성합니다.
  - title: 공개 문서
    details: 설치, 설정, 필드 타입, 액션, 유효성 검사 문서를 한곳에서 제공합니다.
---

## 빠른 설치

```bash
composer require "saaksin/laravel-administrator:10.7.*"
```

서비스 프로바이더를 등록한 뒤 설정 파일을 배포합니다.

```bash
php artisan vendor:publish --provider="SaAkSin\Administrator\AdministratorServiceProvider" --force
```

자세한 내용은 [설치 문서](/docs/ko/installation)를 확인하십시오.
