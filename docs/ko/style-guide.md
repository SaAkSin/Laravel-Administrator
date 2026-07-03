# 스타일 가이드

- [소개](#introduction)
- [여는 중괄호 위치](#starting-braces)
- [공백 대신 탭 사용](#tabs-instead-of-spaces)
- [인라인 주석](#inline-comments)
- [함수/메서드 주석](#function-method-comments)
- [카멜 케이스](#camel-case)
- [줄 바꿈](#line-breaks)
- [공백 제거](#trimming-whitespace)
- [문서 코드 블록](#documentation-code-blocks)

<a name="introduction"></a>
## 소개

모든 코드를 깔끔하고 정돈된 상태로 유지하기 위해 몇 가지 준수해야 할 스타일 규칙이 있습니다. 이것이 전체 규칙 목록은 아니므로, 변경하거나 추가하려는 코드 주변의 작성 스타일을 참고하여 흐름에 맞추는 것이 좋습니다. 만약 여기에 명시된 스타일 중 동의하기 어렵거나 변경해야 할 타당한 이유가 있다면, GitHub 페이지에 이슈를 등록해 함께 이야기해 주세요! 제안해 주신 의견이 코드에 바로 반영되지 않더라도 절대 무시되지 않습니다.

<a name="starting-braces"></a>
## 여는 중괄호 위치

거의 모든 경우에 여는 중괄호는 같은 줄이 아니라 다음 줄에 위치해야 합니다. 이는 [Allman 스타일](http://en.wikipedia.org/wiki/Indent_style#Allman_style)이라고 불립니다. 이 규칙은 `if` 문부터 `function` 선언에 이르기까지 모든 곳에 적용됩니다.

	// 올바른 예시
	if ($something)
	{
		// 무언가를 수행합니다.
	}

	// 잘못된 예시
	if ($something) {

	}

	// 올바른 예시
	if ($somethingElse)
	{

	}
	else
	{

	}

이 규칙에서 유일한 예외는 클래스 이름입니다:

	// 올바른 예시
	class Someting {

	}

<a name="tabs-instead-of-spaces"></a>
## 공백 대신 탭 사용

코드를 작성할 때는 공백(Space) 대신 탭(Tab)을 사용해야 합니다.

<a name="inline-comments"></a>
## 인라인 주석

너무 장황하지 않은 선에서 코드에 인라인 주석을 최대한 많이 남겨두는 것이 좋습니다. 코드를 처음 읽는 사람도 무리 없이 이해할 수 있도록 만드는 것이 가장 이상적입니다. 주석을 작성할 때는 아래와 같이 이중 슬래시(//) 형식을 사용하는 것이 올바른 방법입니다:

	// 가독성을 보장하기 위해 코드를 설명하는 주석을 작성합니다.
	$code = Comment::my_code();

인라인 주석이 더 눈에 잘 띄도록 주석 *위*에는 항상 줄 바꿈을 넣어 주십시오!

<a name="function-method-comments"></a>
## 함수/메서드 주석

함수 또는 메서드 주석은 다음과 같은 형식이어야 합니다:

	/**
	 * 메서드에 대한 설명을 여기에 작성합니다. 필요한 만큼 길게 작성하셔도 좋습니다.
	 *
	 * @param string		$someString
	 * @param int			$someInt
	 *
	 * @return false|array
	 */
	public function myMethod($someString, $someInt)
	{
		if (true)
		{
			return array('yay');
		}
		else
		{
			return false;
		}
	}

매개변수(params)가 없다면 설명과 `@return` 사이에 빈 줄을 하나만 넣으면 됩니다. 반환값(return value)이 없다면 단순히 설명만 포함하면 됩니다.

<a name="camel-case"></a>
## 카멜 케이스

대부분의 경우 Administrator는 PHP와 JavaScript 모두에서 카멜 케이스(camelCase) 스타일을 사용합니다. 이 규칙에서 예외가 적용되는 대표적인 경우는 CSS 클래스 이름과 스네이크 케이스(snake_case)가 사용되는 config(설정) 파일입니다. PHP에서의 작성 예시는 다음과 같습니다:

	/**
	 * 카멜 케이스 예시
	 */
	public function writeTheNameLikeThis()
	{
		// 올바른 예시
		$varName = 'something';

		// 잘못된 예시
		$var_name = 'something_else';
	}

<a name="line-breaks"></a>
## 줄 바꿈

줄 바꿈을 여유 있게 사용하십시오. 코드의 가독성을 극대화하는 것이 목표이며, 줄 바꿈은 가독성을 높이는 핵심적인 요소입니다. 여러 개의 변수를 연속으로 설정하는 경우처럼 연관된 코드끼리는 그룹화할 수 있지만, 논리적으로 타당할 때만 그렇게 하십시오. 모든 주석, 모든 구문(예: `if`, `foreach`), 모든 함수/메서드 주석 등의 앞에는 줄 바꿈을 넣어야 합니다. 코드의 특정 영역에 줄 바꿈을 추가할지 여부를 결정하기 어렵다면, 다른 코드베이스를 훑어보며 유사한 영역을 찾아 지침으로 삼으십시오.

올바른 간격 배치 예시:

	// 선택 사항인 설명 주석
	$someVar = 'value';
	$otherVar = 5;

	// 값이 올바르게 설정되었는지 확인합니다.
	if ($someVar === 'value')
	{
		// 설정이 완료되었으므로 출력합니다.
		echo $someVar;
	}

<a name="trimming-whitespace"></a>
## 공백 제거

가능하다면 각 줄 끝의 공백(trailing whitespace)을 모두 제거해야 합니다. 많은 IDE에서 이 기능을 제공합니다. 예를 들어, SublimeText를 사용하는 경우 사용자 설정에...

	"trim_trailing_white_space_on_save": true

...를 추가할 수 있습니다.

<a name="documentation-code-blocks"></a>
## 문서 코드 블록

문서의 예제 코드는 VitePress의 Shiki 하이라이트가 적용되도록 fenced code block으로 작성합니다. 들여쓰기만으로 만든 코드 블록은 언어 하이라이트와 줄 강조를 사용할 수 없으므로 새 문서에서는 사용하지 않습니다.

````md
```php {3,7-9}
return array(
    'title' => '사용자',
    'model' => App\Models\User::class,
    'columns' => array(
        'name',
    ),
    'edit_fields' => array(
        'name',
    ),
);
```
````

셸 명령은 `bash`, 설정 예시는 `php`, JSON 예시는 `json`, 환경 변수 예시는 `dotenv`, 파일 구조 예시는 `text`를 사용합니다.

````md
```bash
php artisan vendor:publish --tag=laravel-administrator --force
```
````

전역 줄 번호는 VitePress 설정에서 켜져 있습니다. 특정 줄을 강조하려면 코드 블록 언어 뒤에 `{1}`, `{2-4}`, `{1,5-7}` 형식으로 표시합니다.
