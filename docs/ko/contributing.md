# Administrator에 기여하기

- [소개](#introduction)
- [버그, 질문 및 기능 요청](#issues)
- [풀 리퀘스트](#pull-requests)
- [스타일 가이드](#style-guide)

<a name="introduction"></a>
## 소개

Administrator의 소스코드는 [GitHub](https://github.com/FrozenNode/Laravel-Administrator)에서 관리되고 있습니다. MIT 라이선스 하에 배포되므로 자유롭게 포크(fork)하여 원하는 대로 수정 및 사용하실 수 있습니다. Administrator를 활용하여 멋진 프로젝트를 개발하고 계신다면, 언제든 저희에게 공유해 주세요!

<a name="issues"></a>
## 버그, 질문 및 기능 요청

Administrator에서 버그를 발견하셨거나, 문의 사항이 있거나, 새로운 기능을 제안하고 싶으신 경우, 가장 좋은 방법은 [GitHub 이슈 트래커](https://github.com/FrozenNode/Laravel-Administrator/issues)에 이슈를 등록하는 것입니다. Administrator의 수많은 기능 중 상당 부분은 누군가 필요한 기능에 대해 질문하고 제안한 것에서 시작되어 개발되었습니다. 그러니 주저하지 말고 언제든 제안해 주세요!

<a name="pull-requests"></a>
## 풀 리퀘스트

여러분의 풀 리퀘스트(PR) 참여는 언제나 대환영입니다. 제출해주신 모든 PR이 항상 코어 라이브러리에 바로 병합되는 것은 아니지만, 대부분의 PR은 Administrator가 제공할 수 있는 가능성에 대해 고민하고 현재의 아키텍처가 최선인지 검토하는 훌륭한 계기가 됩니다. 풀 리퀘스트를 제출하실 때 신속하게 검토를 진행할 수 있도록 아래 사항들을 준수해 주시기 바랍니다.

- `dev` 브랜치를 기준으로 포크(fork)하고, PR 역시 `dev` 브랜치로 제출해 주십시오. `master` 브랜치로 제출된 PR은 즉시 반려(closed)됩니다.

- 풀 리퀘스트를 제출하기 전에 `dev` 브랜치의 최신 변경 사항을 병합(merge)해 주십시오. 충돌 등으로 인해 자동으로 병합할 수 없는 경우, 최신 변경 사항을 병합한 뒤 다시 제출하도록 요청받을 수 있습니다.

- 추가하거나 변경한 사항에 대한 문서를 `/docs` 디렉토리 내 관련 섹션에 작성해 주십시오.

- 변경 사항을 검증할 수 있는 단위 테스트(unit test)를 필요한 만큼 추가해 주십시오.

- [스타일 가이드](/docs/ko/style-guide)를 준수해 주십시오!

<a name="style-guide"></a>
## 스타일 가이드

스타일 가이드에 대한 자세한 내용은 [스타일 가이드](/docs/ko/style-guide) 페이지를 참고해 주시기 바랍니다.
