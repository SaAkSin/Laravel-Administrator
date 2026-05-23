<?php

use Illuminate\Support\Facades\View;

/**
 * Vite 빌드 머니페스트를 읽어 해시가 포함된 에셋 실경로를 동적으로 반환하고,
 * 연결된 CSS가 존재할 경우 글로벌 CSS 배열에 자동으로 추가합니다.
 *
 * @param string $entry
 * @param array &$cssArray
 * @return string
 */
if (!function_exists('getViteAsset')) {
	function getViteAsset($entry, &$cssArray = null)
	{
		// Vite 5+는 outDir/.vite/manifest.json 경로에 머니페스트를 생성합니다.
		$manifestPath = __DIR__ . '/../public/dist/.vite/manifest.json';
		
		// Vite 4 이하 또는 커스텀 설정 대비 fallback 경로
		if (!file_exists($manifestPath)) {
			$manifestPath = __DIR__ . '/../public/dist/manifest.json';
		}
		
		if (file_exists($manifestPath)) {
			$manifest = json_decode(file_get_contents($manifestPath), true);
			if (isset($manifest[$entry])) {
				$entryData = $manifest[$entry];
				
				// 연관된 CSS 에셋이 존재하는 경우 자동으로 CSS 배열에 추가
				if (is_array($cssArray) && isset($entryData['css']) && is_array($entryData['css'])) {
					foreach ($entryData['css'] as $cssFile) {
						$cssKey = 'vite-' . basename($cssFile, '.css');
						$cssArray[$cssKey] = asset('packages/saaksin/administrator/dist/' . $cssFile);
					}
				}
				
				if (isset($entryData['file'])) {
					return asset('packages/saaksin/administrator/dist/' . $entryData['file']);
				}
			}
		}
		
		// manifest가 존재하지 않는 등의 예외 상황을 대비한 fallback 경로
		return asset('packages/saaksin/administrator/dist/js/app.js');
	}
}

//admin index view
View::composer('administrator::index', function($view)
{
	//get a model instance that we'll use for constructing stuff
	$config = app('itemconfig');
	$fieldFactory = app('admin_field_factory');
	$columnFactory = app('admin_column_factory');
	$actionFactory = app('admin_action_factory');
	$dataTable = app('admin_datatable');
	$model = $config->getDataModel();
	$baseUrl = route('admin_dashboard');
	$route = parse_url($baseUrl);

	//add the view fields
	$view->config = $config;
	$view->dataTable = $dataTable;
	$view->primaryKey = $model->getKeyName();
	$view->editFields = $fieldFactory->getEditFields();
	$view->arrayFields = $fieldFactory->getEditFieldsArrays();
	$view->dataModel = $fieldFactory->getDataModel();
	$view->columnModel = $columnFactory->getColumnOptions();
	$view->actions = $actionFactory->getActionsOptions();
	$view->globalActions = $actionFactory->getGlobalActionsOptions();
	$view->actionPermissions = $actionFactory->getActionPermissions();
	$view->filters = $fieldFactory->getFiltersArrays();
	$view->rows = $dataTable->getRows(app('db'), $view->filters);
	$view->formWidth = $config->getOption('form_width');
	$view->baseUrl = $baseUrl;
	$view->assetUrl = asset('packages/saaksin/administrator/');
	$view->route = $route['path'].'/';
	$view->itemId = isset($view->itemId) ? $view->itemId : null;
});

//admin settings view
View::composer('administrator::settings', function($view)
{
	$config = app('itemconfig');
	$fieldFactory = app('admin_field_factory');
	$actionFactory = app('admin_action_factory');
	$baseUrl = route('admin_dashboard');
	$route = parse_url($baseUrl);

	//add the view fields
	$view->config = $config;
	$view->editFields = $fieldFactory->getEditFields();
	$view->arrayFields = $fieldFactory->getEditFieldsArrays();
	$view->actions = $actionFactory->getActionsOptions();
	$view->baseUrl = $baseUrl;
	$view->assetUrl = asset('packages/saaksin/administrator/');
	$view->route = $route['path'].'/';
});

//header view
View::composer(array('administrator::partials.header'), function($view)
{
	$view->menu = app('admin_menu')->getMenu();
	$view->settingsPrefix = app('admin_config_factory')->getSettingsPrefix();
	$view->pagePrefix = app('admin_config_factory')->getPagePrefix();
	$view->configType = app()->bound('itemconfig') ? app('itemconfig')->getType() : false;
});

//the layout view
View::composer(array('administrator::layouts.default'), function($view)
{
	// 에셋 배열 초기화
	$view->css = array();
	$view->js = array(
		'jquery' => 'https://code.jquery.com/jquery-1.8.2.min.js',
		'jquery-ui' => 'https://code.jquery.com/ui/1.10.3/jquery-ui.min.js',
	);

	if (!$view->page && !$view->dashboard)
	{
		// 1. Vite 컴파일 현대화 에셋 등록 (Alpine.js 및 Tailwind CSS) 및 핵심 레이아웃 main.css 로드
		$view->css += array(
			'main' => asset('packages/saaksin/administrator/css/main.css'),
			'select2' => 'https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.css',
		);
		$view->js += array(
			'select2' => 'https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js',
			'vite-app' => getViteAsset('resources/js/app.js', $view->css),
		);

		// 2. 필수 독립형 바닐라 라이브러리 등록 (jQuery 무관)
		$view->js += array(
			'ckeditor' => 'https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js',
			'markdown' => asset('packages/saaksin/administrator/js/markdown.js'),
			'accounting' => asset('packages/saaksin/administrator/js/accounting.js'),
			'history' => asset('packages/saaksin/administrator/js/history/native.history.js'),
		);
	}

    // 3. 사용자 정의 js 추가
    $customs = config('administrator.custom_js');
    if ($customs) $view->js += $customs;
});
