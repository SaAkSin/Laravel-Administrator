# 커스텀 액션

- [소개](#introduction)
- [모델 항목 액션](#model-actions)
- [모델 전역 액션](#global-actions)
- [설정 페이지 액션](#settings-actions)
- [확인 메시지](#confirmations)
- [동적 메시지](#dynamic-messages)
- [반환값](#return-values)

<a name="introduction"></a>
## 소개

커스텀 액션은 관리자 화면에 버튼을 추가하고, 클릭 시 프로젝트 코드를 실행하는 기능입니다. 모델 설정 파일의 `actions`, `global_actions` 또는 세팅 설정 파일의 `actions`에 선언합니다.

액션은 공통적으로 `title`, `messages`, `permission`, `confirmation`, `action` 옵션을 사용할 수 있습니다.

<a name="model-actions"></a>
## 모델 항목 액션

모델 설정의 `actions`는 특정 모델 항목에 대해 실행됩니다. `action` 콜백은 선택한 Eloquent 모델을 전달받습니다.

```php {2,8,13}
'actions' => array(
    'activate' => array(
        'title' => '활성화',
        'messages' => array(
            'active' => '활성화 중...',
            'success' => '활성화되었습니다.',
            'error' => '활성화에 실패했습니다.',
        ),
        'permission' => function ($model) {
            return auth()->user()->can('update', $model);
        },
        'action' => function ($model) {
            $model->forceFill(array('is_active' => true))->save();

            return true;
        },
    ),
);
```

<a name="global-actions"></a>
## 모델 전역 액션

모델 설정의 `global_actions`는 특정 항목이 아니라 현재 목록 조건에 대해 실행됩니다. `action` 콜백은 필터가 적용된 쿼리 빌더를 전달받습니다.

```php {2,8}
'global_actions' => array(
    'export_csv' => array(
        'title' => 'CSV 다운로드',
        'messages' => array(
            'active' => 'CSV 생성 중...',
            'success' => 'CSV가 생성되었습니다.',
            'error' => 'CSV 생성에 실패했습니다.',
        ),
        'action' => function ($query) {
            $rows = $query->get();

            return response()->streamDownload(function () use ($rows) {
                // CSV 출력
            }, 'export.csv');
        },
    ),
);
```

<a name="settings-actions"></a>
## 설정 페이지 액션

세팅 설정 파일의 `actions`는 현재 설정 데이터를 참조로 전달받습니다. 액션 안에서 데이터를 수정하면 저장 흐름에 반영할 수 있습니다.

```php {2,8}
'actions' => array(
    'clear_page_cache' => array(
        'title' => '페이지 캐시 삭제',
        'messages' => array(
            'active' => '캐시 삭제 중...',
            'success' => '캐시가 삭제되었습니다.',
            'error' => '캐시 삭제에 실패했습니다.',
        ),
        'action' => function (&$data) {
            Cache::forget('pages');

            $data['last_cache_clear_at'] = now()->toDateTimeString();

            return true;
        },
    ),
);
```

<a name="confirmations"></a>
## 확인 메시지

액션 실행 전 확인 대화 상자를 표시하려면 `confirmation`을 지정합니다.

```php {4}
'clear_page_cache' => array(
    'title' => '페이지 캐시 삭제',
    'confirmation' => '정말 페이지 캐시를 삭제하시겠습니까?',
    'action' => function (&$data) {
        Cache::forget('pages');

        return true;
    },
),
```

<a name="dynamic-messages"></a>
## 동적 메시지

`title`, `confirmation`, `messages.active`, `messages.success`, `messages.error`에는 문자열 대신 콜백을 사용할 수 있습니다.

```php {2-4,7-9}
'ban_user' => array(
    'title' => function ($model) {
        return $model->banned ? '차단 해제' : '차단';
    },
    'messages' => array(
        'active' => function ($model) {
            return $model->name . ' 처리 중...';
        },
        'success' => function ($model) {
            return $model->name . ' 처리가 완료되었습니다.';
        },
        'error' => '처리에 실패했습니다.',
    ),
    'action' => function ($model) {
        $model->forceFill(array('banned' => ! $model->banned))->save();

        return true;
    },
),
```

<a name="return-values"></a>
## 반환값

액션 콜백의 반환값은 다음 의미를 가집니다.

| 반환값 | 의미 |
| --- | --- |
| `true` 또는 truthy 값 | 성공 |
| `false` 또는 null | 기본 오류 메시지 |
| 문자열 | 커스텀 오류 메시지 |
| `BinaryFileResponse` | 다운로드 링크 반환 |
| `RedirectResponse` | 프론트엔드에서 해당 URL로 이동 |

액션 성공 뒤 현재 화면을 다시 로드해야 한다면 액션 옵션에 `reload`를 지정합니다.

```php {3}
'clear_cache' => array(
    'title' => '캐시 삭제',
    'reload' => true,
    'action' => function () {
        Cache::flush();

        return true;
    },
),
```

모델 설정의 권한 옵션은 [모델 설정 문서](/docs/ko/model-configuration#permission), 세팅 설정의 액션 작성법은 [세팅 설정 문서](/docs/ko/settings-configuration#custom-actions)를 참고하십시오.
