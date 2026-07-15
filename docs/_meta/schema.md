# 문서 구조와 운영 스키마

이 저장소의 문서는 GitHub 작업 이력을 복제하지 않고 현재 구현과 운영 지식을 설명한다. 작업지시서의 원본은 GitHub Issue body이고, 구현·테스트 결과의 원본은 Pull Request body다.

## 경로별 책임

| 경로 | 책임 | VitePress 게시 |
| --- | --- | --- |
| `docs/*.md` | 영문 공개 매뉴얼과 홈페이지 | 예 |
| `docs/ko/` | 한국어 공개 매뉴얼 | 예 |
| `docs/wiki/` | 코드와 테스트를 종합한 내부 LLM Wiki | 아니오 |
| `docs/_meta/` | 문서 schema, 경로 책임과 문서 지도 | 아니오 |
| `docs/.vitepress/` | VitePress 표시·탐색 설정 | 설정만 사용 |
| `docs/public/` | VitePress 공개 정적 자산 | 예 |
| `raw/` | 외부 원문의 출처·버전별 보존 | 아니오 |

`docs/tasks/`는 사용하지 않는다. 작업 계획, 실행 결과, 테스트 숫자와 커밋 이력은 Issue, Pull Request와 Git history에 남긴다.

## 원본 우선순위

1. 코드와 테스트는 현재 제품 동작의 원본이다.
2. Issue body는 작업 범위와 완료 기준의 원본이다.
3. Pull Request body는 구현·검증 결과의 원본이다.
4. `raw/`는 프로젝트 밖에서 온 규격과 문서의 원본이다.
5. 공개 매뉴얼과 내부 Wiki는 위 원본을 종합한 현재 지식이다.

과거 결과보고서의 문장을 그대로 보존하지 않는다. 현재 코드에서 확인할 수 없는 테스트 결과, 브랜치, 커밋과 일시적 판단은 Wiki로 이전하지 않는다.

## 문서 영향 판정

- `없음`: `docs/`와 `raw/` 변경이 필요 없다.
- `wiki`: 공개 매뉴얼, 내부 Wiki, schema, VitePress 설정 중 하나 이상을 변경한다.
- `raw`: 외부 원문 수집 또는 새 버전 추가가 작업 범위에 명시돼 있다.

`readme.md`, `changelog.md`, `RELEASE_NOTES.md` 같은 저장소 루트 문서는 Arti의 `wiki`·`raw` 분류와 별도로 Issue와 Pull Request에 영향 경로를 기록한다.

## 작성 규칙

- 현재 동작을 먼저 설명하고 변경 이력은 GitHub와 Git에 맡긴다.
- 구현 파일과 테스트 위치를 상대 Markdown 링크로 연결한다.
- 공개 매뉴얼의 내부 링크도 가능한 한 `.md` 상대 경로를 사용한다.
- VitePress route 문자열은 `docs/.vitepress/` 설정 안에서만 `/docs/...` 형식을 사용한다.
- 외부 원문은 프로젝트 해석을 섞지 않고 `raw/<출처>/<문서>/<버전>/` 아래에 보존한다.
- 내부 Wiki는 [Wiki 색인](../wiki/index.md)에서 탐색한다.

## 검증

문서 변경 Pull Request의 최종 Head에서 다음을 실행하고 명령, 종료 코드와 결과를 Pull Request body에 기록한다.

`ARTI_DOCS`는 설치된 `arti-docs` 스킬 디렉터리를 가리킨다.

```bash
BASE_SHA=$(git merge-base github/main HEAD)
python3 "$ARTI_DOCS/scripts/lint_docs.py" \
  --repo . \
  --base "$BASE_SHA" \
  --expect wiki
npm run docs:build
```

`raw/`를 변경하는 명시적 작업에서는 `--expect wiki raw --allow-raw-update`를 사용한다. 문서 변경이 없다면 `--expect none`을 사용한다.
