<?php
namespace SaAkSin\Administrator\Tests;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Validation\Validator as LaravelValidator;
use Laravel\Octane\ApplicationFactory;
use Laravel\Octane\OctaneServiceProvider;
use Laravel\Octane\RequestContext;
use Laravel\Octane\Testing\Fakes\FakeClient;
use Laravel\Octane\Worker;
use Orchestra\Testbench\TestCase;
use ReflectionClass;
use SaAkSin\Administrator\AdministratorServiceProvider;
use SaAkSin\Administrator\Http\Middleware\SetLocale;
use SaAkSin\Administrator\Http\Middleware\ValidateModel;
use SaAkSin\Administrator\Http\Middleware\ValidateSettings;
use SaAkSin\Administrator\Validator as AdministratorValidator;

class OctaneLifecycleModel extends \Illuminate\Database\Eloquent\Model
{
	protected $table = 'octane_lifecycle_models';
}

class HostApplicationValidator extends LaravelValidator
{
}

class OctaneLifecycleState
{
	public static $permission = true;

	public static function permission()
	{
		return static::$permission;
	}

	public static function actionPermission($model)
	{
		return static::$permission;
	}
}

class TestOctaneApplicationFactory extends ApplicationFactory
{
	public $createCount = 0;

	public function __construct(protected Application $application)
	{
	}

	public function createApplication(array $initialInstances = array()): Application
	{
		$this->createCount++;

		foreach ($initialInstances as $key => $value)
		{
			$this->application->instance($key, $value);
		}

		return $this->application;
	}
}

class OctaneLifecycleTest extends TestCase
{
	protected $tempConfigPath;

	public function setUp(): void
	{
		$this->tempConfigPath = sys_get_temp_dir() . '/laravel-administrator-octane-' . bin2hex(random_bytes(6));
		mkdir($this->tempConfigPath, 0755, true);

		$this->writeModelConfig('first_model', '첫 번째 모델');
		$this->writeModelConfig('second_model', '두 번째 모델');
		$this->writeSettingsConfig('site');

		parent::setUp();
	}

	public function tearDown(): void
	{
		foreach (glob($this->tempConfigPath . '/*.php') ?: array() as $file)
		{
			unlink($file);
		}

		if (is_dir($this->tempConfigPath))
		{
			rmdir($this->tempConfigPath);
		}

		OctaneLifecycleState::$permission = true;

		parent::tearDown();
	}

	protected function getPackageProviders($app)
	{
		return array(
			OctaneServiceProvider::class,
			AdministratorServiceProvider::class,
		);
	}

	protected function defineEnvironment($app)
	{
		$app['config']->set('app.locale', 'en');
		$app['config']->set('administrator.permission', function()
		{
			return true;
		});
		$app['config']->set('administrator.middleware', array('web'));
		$app['config']->set('administrator.model_config_path', $this->tempConfigPath);
		$app['config']->set('administrator.settings_config_path', $this->tempConfigPath);
		$app['config']->set('administrator.menu', array('first_model', 'second_model', 'settings.site', 'page.custom'));
		$app['config']->set('administrator.locales', array('en', 'ko'));
		$app['config']->set('administrator.global_rows_per_page', 20);
	}

	public function testScopedServicesAreReusedOnlyWithinTheCurrentLifecycle()
	{
		$firstSession = $this->makeSession(array('administrator_first_model_rows_per_page' => 75));
		$this->bindRouteRequest('model', 'first_model', $firstSession);
		$this->app->instance('session.store', $firstSession);
		OctaneLifecycleState::$permission = true;

		$serviceNames = array(
			'admin_validator',
			'admin_config_factory',
			'itemconfig',
			'admin_field_factory',
			'admin_datatable',
			'admin_column_factory',
			'admin_action_factory',
			'admin_menu',
		);
		$firstLifecycle = array();

		foreach ($serviceNames as $serviceName)
		{
			$firstLifecycle[$serviceName] = $this->app->make($serviceName);
			$this->assertSame($firstLifecycle[$serviceName], $this->app->make($serviceName));
		}

		$this->assertSame('첫 번째 모델', $firstLifecycle['itemconfig']->getOption('title'));
		$this->assertTrue($firstLifecycle['itemconfig']->getOption('permission'));
		$this->assertTrue($firstLifecycle['admin_action_factory']->getActionPermissions()['update']);
		$this->assertSame(75, $firstLifecycle['admin_datatable']->getRowsPerPage());

		$this->app->forgetScopedInstances();

		$secondSession = $this->makeSession(array('administrator_second_model_rows_per_page' => 15));
		$this->bindRouteRequest('model', 'second_model', $secondSession);
		$this->app->instance('session.store', $secondSession);
		OctaneLifecycleState::$permission = false;

		foreach ($serviceNames as $serviceName)
		{
			$this->assertNotSame($firstLifecycle[$serviceName], $this->app->make($serviceName));
		}

		$this->assertSame('두 번째 모델', $this->app->make('itemconfig')->getOption('title'));
		$this->assertFalse($this->app->make('itemconfig')->getOption('permission'));
		$this->assertFalse($this->app->make('admin_action_factory')->getActionPermissions()['update']);
		$this->assertSame(15, $this->app->make('admin_datatable')->getRowsPerPage());
	}

	public function testItemConfigBindingIsRegisteredOnceAndResolvesModelAndSettingsRoutes()
	{
		$scopedCount = $this->scopedRegistrationCount();

		$modelRequest = $this->bindRouteRequest('model', 'first_model', $this->makeSession());
		(new ValidateModel())->handle($modelRequest, function($request)
		{
			return $request;
		});

		$this->assertSame('model', $this->app->make('itemconfig')->getType());
		$this->assertSame($scopedCount, $this->scopedRegistrationCount());

		$this->app->forgetScopedInstances();
		$settingsRequest = $this->bindRouteRequest('settings', 'site', $this->makeSession());
		(new ValidateSettings())->handle($settingsRequest, function($request)
		{
			return $request;
		});

		$this->assertSame('settings', $this->app->make('itemconfig')->getType());
		$this->assertSame($scopedCount, $this->scopedRegistrationCount());
	}

	public function testFilterStateIsClearedBetweenRequestLifecycles()
	{
		$this->bindRouteRequest('model', 'first_model', $this->makeSession());
		$firstFilter = $this->app->make('admin_field_factory')->findFilter('id');
		$firstFilter->setFilter(array(
			'value' => '42',
			'min_value' => '1,000',
			'max_value' => '2,000',
		));

		$this->assertSame('42', $firstFilter->getOption('value'));
		$this->assertSame('1000', $firstFilter->getOption('min_value'));
		$this->assertSame('2000', $firstFilter->getOption('max_value'));

		$this->app->forgetScopedInstances();
		$this->bindRouteRequest('model', 'second_model', $this->makeSession());
		$secondFilter = $this->app->make('admin_field_factory')->findFilter('id');

		$this->assertNotSame($firstFilter, $secondFilter);
		$this->assertSame('', $secondFilter->getOption('value'));
		$this->assertSame('', $secondFilter->getOption('min_value'));
		$this->assertSame('', $secondFilter->getOption('max_value'));
	}

	public function testOctaneWorkerBootsOnceAndHandlesTwoIsolatedRequests()
	{
		$this->assertTrue($this->app->providerIsLoaded(OctaneServiceProvider::class));

		$this->app->make('router')->get('/__octane-lifecycle/{model}', function(Request $request)
		{
			$config = app('itemconfig');
			$fieldFactory = app('admin_field_factory');
			$filter = $fieldFactory->findFilter('id');

			if ($request->hasAny(array('value', 'min_value', 'max_value')))
			{
				$filter->setFilter($request->only(array('value', 'min_value', 'max_value')));
			}

			return response()->json(array(
				'title' => $config->getOption('title'),
				'permission' => $config->getOption('permission'),
				'action_permission' => app('admin_action_factory')->getActionPermissions()['update'],
				'filter' => array(
					'value' => $filter->getOption('value'),
					'min_value' => $filter->getOption('min_value'),
					'max_value' => $filter->getOption('max_value'),
				),
			));
		})->middleware(ValidateModel::class);

		$client = new FakeClient(array());
		$applicationFactory = new TestOctaneApplicationFactory($this->app);
		$worker = new Worker($applicationFactory, $client);
		$lifecycleObjects = array();
		$worker->onRequestHandled(function($request, $response, $sandbox) use (&$lifecycleObjects)
		{
			$lifecycleObjects[] = array(
				'sandbox' => $sandbox,
				'itemconfig' => $sandbox->make('itemconfig'),
				'field_factory' => $sandbox->make('admin_field_factory'),
				'action_factory' => $sandbox->make('admin_action_factory'),
			);
		});
		$worker->boot();
		$workerApplication = $worker->application();

		OctaneLifecycleState::$permission = true;
		$firstRequest = Request::create('/__octane-lifecycle/first_model?value=42&min_value=1%2C000&max_value=2%2C000', 'GET');
		$worker->handle($firstRequest, new RequestContext(array('request' => $firstRequest)));

		OctaneLifecycleState::$permission = false;
		$secondRequest = Request::create('/__octane-lifecycle/second_model', 'GET');
		$worker->handle($secondRequest, new RequestContext(array('request' => $secondRequest)));

		$this->assertSame($workerApplication, $worker->application());
		$this->assertSame(1, $applicationFactory->createCount);
		$this->assertCount(2, $client->responses);
		$this->assertSame(array(), $client->errors);
		$this->assertCount(2, $lifecycleObjects);
		$this->assertNotSame($lifecycleObjects[0]['sandbox'], $lifecycleObjects[1]['sandbox']);
		$this->assertNotSame($lifecycleObjects[0]['itemconfig'], $lifecycleObjects[1]['itemconfig']);
		$this->assertNotSame($lifecycleObjects[0]['field_factory'], $lifecycleObjects[1]['field_factory']);
		$this->assertNotSame($lifecycleObjects[0]['action_factory'], $lifecycleObjects[1]['action_factory']);

		$firstResponse = json_decode($client->responses[0]->getContent(), true);
		$secondResponse = json_decode($client->responses[1]->getContent(), true);

		$this->assertSame('첫 번째 모델', $firstResponse['title']);
		$this->assertTrue($firstResponse['permission']);
		$this->assertTrue($firstResponse['action_permission']);
		$this->assertSame(array('value' => '42', 'min_value' => '1000', 'max_value' => '2000'), $firstResponse['filter']);
		$this->assertSame('두 번째 모델', $secondResponse['title']);
		$this->assertFalse($secondResponse['permission']);
		$this->assertFalse($secondResponse['action_permission']);
		$this->assertSame(array('value' => '', 'min_value' => '', 'max_value' => ''), $secondResponse['filter']);
	}

	public function testRoutesWithoutItemConfigDoNotResolveIt()
	{
		$this->bindRouteRequest('model', 'first_model', $this->makeSession());
		$this->app->make('itemconfig');

		foreach (array('dashboard' => null, 'page' => 'custom', 'path' => 'js/app.js') as $parameter => $value)
		{
			$this->app->forgetScopedInstances();
			$this->bindRouteRequest($parameter, $value, $this->makeSession());

			// Container의 resolved 이력은 lifecycle 종료 뒤에도 남으므로 라우트 parameter를 기준으로 판단해야 한다.
			$this->assertTrue($this->app->resolved('itemconfig'));
			$this->assertStringContainsString('id="filter_button" class="hidden"', view('administrator::partials.header')->render());

			try
			{
				$this->app->make('itemconfig');
				$this->fail('항목 설정이 없는 라우트에서는 itemconfig가 해석되면 안 됩니다.');
			}
			catch (BindingResolutionException $exception)
			{
				$this->assertStringContainsString('항목 설정', $exception->getMessage());
			}
		}
	}

	public function testAdministratorValidatorDoesNotReplaceTheHostResolver()
	{
		$factory = $this->app->make('validator');
		$factory->resolver(function($translator, $data, $rules, $messages, $attributes)
		{
			return new HostApplicationValidator($translator, $data, $rules, $messages, $attributes);
		});
		$factory->extend('host_only', function()
		{
			return true;
		});

		$this->assertInstanceOf(HostApplicationValidator::class, $factory->make(array(), array()));

		$administratorValidator = $this->app->make('admin_validator');
		$administratorValidator->override(array('value' => 'ok'), array('value' => 'host_only'));

		$this->assertInstanceOf(AdministratorValidator::class, $administratorValidator);
		$this->assertTrue($administratorValidator->passes());
		$this->assertInstanceOf(HostApplicationValidator::class, $factory->make(array(), array()));
	}

	public function testLocaleIsAppliedPerSessionAndFallsBackToTheApplicationDefault()
	{
		$middleware = new SetLocale();
		$allowedSession = $this->makeSession(array('administrator_locale' => 'ko'));
		$allowedRequest = Request::create('/admin', 'GET');
		$allowedRequest->setLaravelSession($allowedSession);

		$middleware->handle($allowedRequest, function()
		{
			$this->assertSame('ko', app()->getLocale());
		});

		$invalidSession = $this->makeSession(array('administrator_locale' => 'fr'));
		$invalidRequest = Request::create('/admin', 'GET');
		$invalidRequest->setLaravelSession($invalidSession);
		config()->set('app.locale', 'en');
		$middleware->handle($invalidRequest, function()
		{
			$this->assertSame('en', app()->getLocale());
		});

		app('translator')->setLocale('ko');
		config()->set('app.locale', 'en');
		$emptyRequest = Request::create('/admin', 'GET');
		$emptyRequest->setLaravelSession($this->makeSession());
		$middleware->handle($emptyRequest, function()
		{
			$this->assertSame('en', app()->getLocale());
		});
	}

	public function testLocaleMiddlewareRunsAfterTheWebSessionMiddleware()
	{
		$route = $this->app->make('router')->getRoutes()->getByName('admin_dashboard');
		$middleware = array_values($route->gatherMiddleware());
		$webIndex = array_search('web', $middleware, true);
		$localeIndex = array_search(SetLocale::class, $middleware, true);

		$this->assertNotFalse($webIndex);
		$this->assertNotFalse($localeIndex);
		$this->assertGreaterThan($webIndex, $localeIndex);
	}

	protected function bindRouteRequest($parameter, $value, Store $session)
	{
		$request = Request::create('/admin/' . ($value ?: ''), 'GET');
		$route = new Route('GET', '/admin/{value?}', function()
		{
		});
		$route->bind($request);

		if (!is_null($value))
		{
			$route->setParameter($parameter, $value);
		}

		$request->setRouteResolver(function() use ($route)
		{
			return $route;
		});
		$request->setLaravelSession($session);
		$this->app->instance('request', $request);

		return $request;
	}

	protected function makeSession(array $values = array())
	{
		$session = new Store('octane-lifecycle', new ArraySessionHandler(120));
		$session->start();
		$session->put($values);

		return $session;
	}

	protected function scopedRegistrationCount()
	{
		$reflection = new ReflectionClass(\Illuminate\Container\Container::class);
		$property = $reflection->getProperty('scopedInstances');

		return count($property->getValue($this->app));
	}

	protected function writeModelConfig($name, $title)
	{
		$config = array(
			'title' => $title,
			'single' => $title,
			'model' => OctaneLifecycleModel::class,
			'permission' => array(OctaneLifecycleState::class, 'permission'),
			'action_permissions' => array('update' => array(OctaneLifecycleState::class, 'actionPermission')),
			'columns' => array('id' => array('title' => 'ID')),
			'edit_fields' => array('id' => array('type' => 'key')),
			'filters' => array('id' => array('type' => 'number')),
		);

		file_put_contents($this->tempConfigPath . '/' . $name . '.php', '<?php return ' . var_export($config, true) . ';');
	}

	protected function writeSettingsConfig($name)
	{
		$config = array(
			'title' => '사이트 설정',
			'edit_fields' => array('title' => array('type' => 'text')),
			'storage_path' => $this->tempConfigPath,
		);

		file_put_contents($this->tempConfigPath . '/' . $name . '.php', '<?php return ' . var_export($config, true) . ';');
	}
}
