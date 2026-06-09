<?php
namespace SaAkSin\Administrator\Tests;

use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use SaAkSin\Administrator\AdminController;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TestContainer extends \Illuminate\Container\Container
{
	public function abort($code, $message = '', array $headers = [])
	{
		throw new \Symfony\Component\HttpKernel\Exception\HttpException($code, $message, null, $headers);
	}
}

class SecureAssetTest extends \PHPUnit\Framework\TestCase
{
	protected $container;
	protected $originalContainer;
	protected $originalFacadeApp;

	public function setUp(): void
	{
		parent::setUp();
		
		// 기존 컨테이너 및 파사드 어플리케이션 백업
		$this->originalContainer = Container::getInstance();
		$this->originalFacadeApp = Facade::getFacadeApplication();

		// 가짜 Laravel 컨테이너 설정
		$this->container = new TestContainer();
		Container::setInstance($this->container);
		Facade::setFacadeApplication($this->container);

		// url generator 모킹
		$urlGenerator = m::mock('Illuminate\Routing\UrlGenerator');
		$urlGenerator->shouldReceive('route')->andReturnUsing(function($name, $parameters = []) {
			return "http://localhost/packages/saaksin/administrator/secure-dist/" . ($parameters['path'] ?? '');
		});
		$this->container->instance('url', $urlGenerator);

		// response factory 모킹
		$responseFactory = m::mock('Illuminate\Routing\ResponseFactory');
		$responseFactory->shouldReceive('file')->andReturnUsing(function($path, $headers = []) {
			return new \Symfony\Component\HttpFoundation\BinaryFileResponse($path, 200, $headers);
		});
		$this->container->instance(\Illuminate\Contracts\Routing\ResponseFactory::class, $responseFactory);
		$this->container->instance('response', $responseFactory);

		// view factory 모킹
		$viewFactory = m::mock('Illuminate\Contracts\View\Factory');
		$viewFactory->shouldReceive('make')->andReturn(m::mock('Illuminate\Contracts\View\View', function($mock) {
			$mock->shouldReceive('with')->andReturnSelf();
			$mock->page = false;
			$mock->dashboard = false;
		}));
		$viewFactory->shouldReceive('composer')->zeroOrMoreTimes();
		$this->container->instance('view', $viewFactory);
		$this->container->instance(\Illuminate\Contracts\View\Factory::class, $viewFactory);

		// 이제 컨테이너와 view 서비스가 셋업되었으므로 viewComposers.php를 include 합니다.
		require_once __DIR__ . '/../src/viewComposers.php';
	}

	public function tearDown(): void
	{
		m::close();
		
		// 기존 상태 복원
		Container::setInstance($this->originalContainer);
		Facade::setFacadeApplication($this->originalFacadeApp);
		Facade::clearResolvedInstances();
		
		parent::tearDown();
	}

	/**
	 * HMR이 비활성화된 상태에서 getViteAsset 헬퍼가 route를 통해 프로덕션용 에셋 경로를 잘 반환하는지 테스트
	 */
	public function testGetViteAssetProduction()
	{
		// 임시로 manifest.json 생성
		$distDir = __DIR__ . '/../public/dist';
		if (!is_dir($distDir)) {
			mkdir($distDir, 0755, true);
		}
		
		$manifestPath = $distDir . '/manifest.json';
		$manifestData = [
			'resources/js/app.js' => [
				'file' => 'js/app.12345.js',
				'css' => ['css/app.67890.css']
			]
		];
		file_put_contents($manifestPath, json_encode($manifestData));

		// hot 파일이 없는 상태에서 호출
		$hotPath = $distDir . '/hot';
		if (file_exists($hotPath)) {
			unlink($hotPath);
		}

		$cssArray = [];
		$jsUrl = getViteAsset('resources/js/app.js', $cssArray);

		$this->assertStringContainsString('http://localhost/packages/saaksin/administrator/secure-dist/', $jsUrl);
		if (!empty($cssArray)) {
			foreach ($cssArray as $key => $val) {
				$this->assertStringStartsWith('vite-', $key);
				$this->assertStringContainsString('http://localhost/packages/saaksin/administrator/secure-dist/', $val);
			}
		}

		// 정리
		unlink($manifestPath);
	}

	/**
	 * HMR이 활성화된 상태에서 getViteAsset 헬퍼가 개발서버 주소를 잘 반환하는지 테스트
	 */
	public function testGetViteAssetHmr()
	{
		$distDir = __DIR__ . '/../public/dist';
		if (!is_dir($distDir)) {
			mkdir($distDir, 0755, true);
		}
		
		$hotPath = $distDir . '/hot';
		file_put_contents($hotPath, 'http://localhost:5173');

		$cssArray = [];
		$jsUrl = getViteAsset('resources/js/app.js', $cssArray);

		$this->assertEquals('http://localhost:5173/resources/js/app.js', $jsUrl);
		$this->assertEmpty($cssArray);

		// 정리
		unlink($hotPath);
	}

	/**
	 * AdminController의 serveAsset이 Directory Traversal 시도를 차단하는지 테스트
	 */
	public function testServeAssetDirectoryTraversalProtection()
	{
		// AdminController 인스턴스 생성
		$request = m::mock(Request::class);
		$session = m::mock(SessionManager::class);
		
		$controller = new AdminController($request, $session);

		// Directory Traversal 시도 경로
		// __DIR__ . '/../../public/dist' -> laravel-administrator/public/dist 임.
		// 만약 'path'에 '../../composer.json' 등을 넣는다면
		// $realPath는 패키지 루트의 composer.json 물리 경로가 됨.
		// 이는 $basePath 하위가 아니므로 403 Forbidden을 반환해야 함.
		
		$this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
		
		try {
			$controller->serveAsset('../../composer.json');
		} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
			$this->assertEquals(403, $e->getStatusCode());
			throw $e;
		}
	}

	/**
	 * AdminController의 serveAsset이 존재하지 않는 파일 접근 시 404를 반환하는지 테스트
	 */
	public function testServeAssetNotFound()
	{
		$request = m::mock(Request::class);
		$session = m::mock(SessionManager::class);
		$controller = new AdminController($request, $session);

		$this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
		
		try {
			$controller->serveAsset('non_existent_file.js');
		} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
			$this->assertEquals(404, $e->getStatusCode());
			throw $e;
		}
	}

	/**
	 * AdminController의 serveAsset이 정상적으로 에셋을 서빙하고 Mime-Type과 Cache-Control 헤더를 잘 적용하는지 테스트
	 */
	public function testServeAssetSuccess()
	{
		// 임시 에셋 파일 생성
		$distDir = __DIR__ . '/../public/dist';
		if (!is_dir($distDir)) {
			mkdir($distDir, 0755, true);
		}
		
		$testFile = $distDir . '/test_asset.js';
		file_put_contents($testFile, 'console.log("hello");');

		$request = m::mock(Request::class);
		$session = m::mock(SessionManager::class);
		$controller = new AdminController($request, $session);

		$response = $controller->serveAsset('test_asset.js');

		$this->assertInstanceOf(BinaryFileResponse::class, $response);
		$this->assertEquals('application/javascript; charset=utf-8', $response->headers->get('Content-Type'));
		
		$cacheControl = $response->headers->get('Cache-Control');
		$this->assertStringContainsString('public', $cacheControl);
		$this->assertStringContainsString('max-age=31536000', $cacheControl);
		$this->assertStringContainsString('immutable', $cacheControl);

		// 정리
		unlink($testFile);
	}

	/**
	 * AdminController의 serveAsset이 동일한 접두사를 가지는 형제 디렉토리 우회(Partial Path Traversal)를 차단하는지 테스트
	 */
	public function testServeAssetPartialPathTraversalProtection()
	{
		// 1. 임시 형제 디렉토리 및 비밀 파일 생성
		$secretDir = __DIR__ . '/../public/dist-secret';
		if (!is_dir($secretDir)) {
			mkdir($secretDir, 0755, true);
		}
		$secretFile = $secretDir . '/secret.txt';
		file_put_contents($secretFile, 'secret data');

		$request = m::mock(Request::class);
		$session = m::mock(SessionManager::class);
		$controller = new AdminController($request, $session);

		$this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
		
		try {
			// 2. dist 디렉토리 상위로 이동 후 dist-secret/secret.txt에 대한 우회 접근 시도
			$controller->serveAsset('../dist-secret/secret.txt');
		} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
			// 3. 403 Forbidden 상태 코드가 반환되는지 확인
			$this->assertEquals(403, $e->getStatusCode());
			throw $e;
		} finally {
			// 4. 리소스 정리
			if (file_exists($secretFile)) {
				unlink($secretFile);
			}
			if (is_dir($secretDir)) {
				rmdir($secretDir);
			}
		}
	}

	/**
	 * Actions\Factory의 parseDefaults 및 getActionPermissions가
	 * 사용자 지정 파라미터명($item)을 가진 클로저 권한 설정을 하위 호환성 단절 없이 정상 처리하는지 테스트
	 */
	public function testActionFactoryClosureParameterCompatibility()
	{
		// 1. Mock 설정
		$validator = m::mock('SaAkSin\Administrator\Validator');
		$validator->shouldReceive('arrayGet')->andReturnUsing(function($array, $key, $default) {
			return isset($array[$key]) ? $array[$key] : $default;
		});

		$config = m::mock('SaAkSin\Administrator\Config\ConfigInterface');
		
		// 테스트용 임시 모델 구성
		$dummyModel = new \stdClass();
		$dummyModel->id = 123;
		$dummyModel->exists = true;

		$config->shouldReceive('getDataModel')->andReturn($dummyModel);
		$config->shouldReceive('getOption')->with('action_name')->andReturn('test_action');
		
		// 2. parseDefaults 호환성 테스트
		// 클로저 인자명을 $item으로 지정하고 model 속성을 확인하는 콜백 정의
		$customClosure = function($item) {
			return isset($item->id) && $item->id === 123;
		};

		$factory = new \SaAkSin\Administrator\Actions\Factory($validator, $config);

		$options = [
			'permission' => $customClosure
		];

		$parsed = $factory->parseDefaults('edit', $options);
		
		// 에러 발생 없이 정상적으로 참(True)이 반환되는지 검증
		$this->assertTrue($parsed['has_permission']);

		// 3. getActionPermissions 호환성 테스트
		$config->shouldReceive('getOption')->with('action_permissions')->andReturn([
			'update' => function($item) {
				return $item->exists === true;
			}
		]);

		$permissions = $factory->getActionPermissions(true);
		
		// 에러 발생 없이 정상적으로 권한 설정 결과가 반영되는지 검증
		$this->assertTrue($permissions['update']);
	}
}
