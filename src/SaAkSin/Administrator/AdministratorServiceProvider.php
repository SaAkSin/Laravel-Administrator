<?php namespace SaAkSin\Administrator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\BindingResolutionException;
use SaAkSin\Administrator\DataTable\DataTable;
use Illuminate\Support\Facades\Validator as LValidator;
use SaAkSin\Administrator\Fields\Factory as FieldFactory;
use SaAkSin\Administrator\Config\Factory as ConfigFactory;
use SaAkSin\Administrator\Actions\Factory as ActionFactory;
use SaAkSin\Administrator\DataTable\Columns\Factory as ColumnFactory;

class AdministratorServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->loadViewsFrom(__DIR__.'/../../views', 'administrator');

		$this->loadTranslationsFrom(__DIR__.'/../../lang', 'administrator');

		$this->loadRoutesFrom(__DIR__.'/../../routes.php');

		$this->publishes([
			__DIR__.'/../../config/administrator.php' => config_path('administrator.php'),
		]);

		$this->publishes([
			__DIR__.'/../../../public' => public_path('packages/saaksin/administrator'),
		], 'laravel-administrator');

		$this->publishes([
			__DIR__.'/../../../public' => public_path('packages/saaksin/administrator'),
		], 'public');

		// 이 이벤트는 애플리케이션 또는 장기 실행 워커가 부트될 때 한 번만 발생한다.
		// 요청별 상태 초기화에는 scoped binding과 관리자 요청 미들웨어를 사용해야 한다.
		$this->app['events']->dispatch('administrator.ready');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__.'/../../config/administrator.php', 'administrator'
		);

		//include our view composers to avoid issues with catch-all routes defined by users
		include __DIR__.'/../../viewComposers.php';

		// 공유 validation factory를 변경하지 않는 관리자 전용 validator
		$this->app->scoped('admin_validator',function($app)
		{
			$validatorFactory = clone $app->make('validator');
			$validatorFactory->resolver(function($translator, $data, $rules, $messages, $customAttributes) use ($app)
			{
				$validator = new Validator($translator, $data, $rules, $messages, $customAttributes);
				$validator->setUrlInstance($app->make('url'));
				return $validator;
			});

			return $validatorFactory->make(array(), array());
		});

		// 요청 또는 작업 생명주기마다 격리되는 관리자 상태 서비스
		$this->app->scoped('admin_config_factory',function($app)
		{
			return new ConfigFactory($app->make('admin_validator'), LValidator::make(array(), array()), config('administrator'));
		});

		$this->app->scoped('itemconfig', function($app)
		{
			$request = $app->make('request');
			$route = $request->route();

			if (!$route)
			{
				throw new BindingResolutionException('현재 요청에는 관리자 항목 설정 라우트가 없습니다.');
			}

			$configFactory = $app->make('admin_config_factory');
			$modelName = $route->parameter('model');

			if (!is_null($modelName))
			{
				return $configFactory->make($modelName, true);
			}

			$settingsName = $route->parameter('settings');

			if (!is_null($settingsName))
			{
				return $configFactory->make($configFactory->getSettingsPrefix() . $settingsName, true);
			}

			throw new BindingResolutionException('현재 관리자 라우트에는 항목 설정이 없습니다.');
		});

		$this->app->scoped('admin_field_factory',function($app)
		{
			return new FieldFactory($app->make('admin_validator'), $app->make('itemconfig'), $app->make('db'));
		});

		$this->app->scoped('admin_datatable',function($app)
		{
			$dataTable = new DataTable($app->make('itemconfig'), $app->make('admin_column_factory'), $app->make('admin_field_factory'));
			$dataTable->setRowsPerPage($app->make('session.store'), config('administrator.global_rows_per_page'));

			return $dataTable;
		});

		$this->app->scoped('admin_column_factory',function($app)
		{
			return new ColumnFactory($app->make('admin_validator'), $app->make('itemconfig'), $app->make('db'));
		});

		$this->app->scoped('admin_action_factory',function($app)
		{
			return new ActionFactory($app->make('admin_validator'), $app->make('itemconfig'), $app->make('db'));
		});

		$this->app->scoped('admin_menu',function($app)
		{
			return new Menu($app->make('config'), $app->make('admin_config_factory'));
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('admin_validator', 'admin_config_factory', 'itemconfig', 'admin_field_factory', 'admin_datatable', 'admin_column_factory',
			'admin_action_factory', 'admin_menu');
	}

}
