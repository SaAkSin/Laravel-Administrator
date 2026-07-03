<?php
namespace SaAkSin\Administrator\Tests;

use Orchestra\Testbench\TestCase;
use SaAkSin\Administrator\AdministratorServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;

/**
 * 통합 테스트용 더미 모델 선언
 */
class IntegrationTestModel extends Model
{
	protected $table = 'integration_tests';
}

class AdministratorIntegrationTest extends TestCase
{
	protected $tempConfigPath;
	protected $backupManifests = [];

	public function setUp(): void
	{
		// 1. 임시 설정 디렉토리 및 파일 생성
		$this->tempConfigPath = __DIR__ . '/temp_config';
		if (!is_dir($this->tempConfigPath)) {
			mkdir($this->tempConfigPath, 0755, true);
		}

		$modelConfig = [
			'title' => 'Valid Model',
			'single' => 'valid model',
			'model' => IntegrationTestModel::class,
			'columns' => [
				'id' => [
					'title' => 'ID',
				],
			],
			'edit_fields' => [
				'id' => [
					'type' => 'key',
				],
			],
		];

		file_put_contents(
			$this->tempConfigPath . '/valid_model.php',
			'<?php return ' . var_export($modelConfig, true) . ';'
		);

		// 2. 임시 manifest.json 백업 및 생성
		$distDir = __DIR__ . '/../public/dist';
		$manifestPaths = [
			$distDir . '/manifest.json',
			$distDir . '/.vite/manifest.json'
		];

		$manifestData = [
			'resources/js/app.ts' => [
				'file' => 'js/app.12345.js',
				'css' => ['css/app.67890.css']
			],
			'resources/css/themes/silver.css' => [
				'file' => 'css/silver.54321.css'
			]
		];

		foreach ($manifestPaths as $path) {
			if (file_exists($path)) {
				$this->backupManifests[$path] = file_get_contents($path);
			}
			
			$dir = dirname($path);
			if (!is_dir($dir)) {
				mkdir($dir, 0755, true);
			}
			file_put_contents($path, json_encode($manifestData));
		}

		parent::setUp();
	}

	public function tearDown(): void
	{
		// 1. Manifest 복원 및 임시 파일 제거
		$distDir = __DIR__ . '/../public/dist';
		$manifestPaths = [
			$distDir . '/manifest.json',
			$distDir . '/.vite/manifest.json'
		];

		foreach ($manifestPaths as $path) {
			if (isset($this->backupManifests[$path])) {
				file_put_contents($path, $this->backupManifests[$path]);
			} else {
				if (file_exists($path)) {
					unlink($path);
				}
			}
		}

		// 2. 임시 디렉토리 및 파일 정리
		if (is_dir($this->tempConfigPath)) {
			if (file_exists($this->tempConfigPath . '/valid_model.php')) {
				unlink($this->tempConfigPath . '/valid_model.php');
			}
			rmdir($this->tempConfigPath);
		}

		parent::tearDown();
	}

	/**
	 * 패키지 서비스 프로바이더 등록
	 */
	protected function getPackageProviders($app)
	{
		return [
			AdministratorServiceProvider::class,
		];
	}

	/**
	 * 테스트용 기본 설정 주입
	 */
	protected function defineEnvironment($app)
	{
		// 임시 설정 정의
		$app['config']->set('administrator.theme', 'silver');
		$app['config']->set('administrator.themes', [
			'silver' => [
				'label' => '실버',
				'entry' => 'resources/css/themes/silver.css',
			],
			'legacy' => [
				'label' => '레거시',
				'entry' => null,
			],
		]);
		$app['config']->set('administrator.custom_css', [
			'custom-style' => 'http://localhost/css/custom.css',
		]);
		$app['config']->set('administrator.custom_js', [
			'custom-script' => 'http://localhost/js/custom.js',
		]);

		$app['config']->set('administrator.model_config_path', __DIR__ . '/temp_config');
		$app['config']->set('administrator.settings_config_path', __DIR__ . '/temp_config');
		$app['config']->set('administrator.menu', ['valid_model']);
		$app['config']->set('administrator.title', 'Admin');
	}

	/**
	 * 서비스 프로바이더 부트 및 라우트 등록 검증
	 */
	public function testServiceProviderBootAndRoutesLoaded()
	{
		// 라우트 로드 검증
		$this->assertTrue(Route::has('admin_dashboard'));
		$this->assertTrue(Route::has('admin_secure_asset'));
	}

	/**
	 * theme=silver 설정 시 실버 테마 CSS 로드 검증
	 */
	public function testThemeSilverLoading()
	{
		// default 레이아웃 뷰 생성 및 바인딩 데이터 검증
		$view = view('administrator::layouts.default', ['content' => '']);
		$view->render();
		$data = $view->getData();

		// 테마 CSS 주입 확인
		$this->assertArrayHasKey('theme-silver', $data['css']);
		$this->assertStringContainsString('css/silver.54321.css', $data['css']['theme-silver']);
	}

	/**
	 * theme=legacy 설정 시 테마 CSS 로딩 생략 검증
	 */
	public function testThemeLegacyLoading()
	{
		$this->app['config']->set('administrator.theme', 'legacy');

		// default 레이아웃 뷰 생성
		$view = view('administrator::layouts.default', ['content' => '']);
		$view->render();
		$data = $view->getData();

		// 테마 CSS가 주입되지 않아야 함
		$this->assertArrayNotHasKey('theme-legacy', $data['css']);
		$this->assertArrayNotHasKey('theme-silver', $data['css']);
	}

	/**
	 * 잘못된 테마 값은 silver로 fallback하는지 검증
	 */
	public function testThemeFallbackToSilver()
	{
		// 존재하지 않는 테마 지정
		$this->app['config']->set('administrator.theme', 'invalid-theme');

		$view = view('administrator::layouts.default', ['content' => '']);
		$view->render();
		$data = $view->getData();

		// silver로 fallback되어 실버 테마가 포함되어야 함
		$this->assertArrayHasKey('theme-silver', $data['css']);
		$this->assertStringContainsString('css/silver.54321.css', $data['css']['theme-silver']);
	}

	/**
	 * custom_css 및 custom_js가 정상 로드 및 결합되는지 검증
	 */
	public function testCustomCssAndJsLoading()
	{
		$view = view('administrator::layouts.default', ['content' => '']);
		$view->render();
		$data = $view->getData();

		// custom_css와 custom_js가 주입되었는지 확인
		$this->assertArrayHasKey('custom-style', $data['css']);
		$this->assertEquals('http://localhost/css/custom.css', $data['css']['custom-style']);

		$this->assertArrayHasKey('custom-script', $data['js']);
		$this->assertEquals('http://localhost/js/custom.js', $data['js']['custom-script']);
	}
}
