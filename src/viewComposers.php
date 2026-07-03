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
	/**
	 * Vite 빌드 머니페스트를 읽어 안전 에셋 로더 라우트 주소를 반환합니다.
	 * HMR 서버 활성화 시에는 해당 개발 서버 URL을 다이렉트로 반환합니다.
	 *
	 * @param string $entry
	 * @param array &$cssArray
	 * @return string
	 */
	function getViteAsset($entry, &$cssArray = null)
	{
		$hotPath = __DIR__ . '/../public/dist/hot';
		
		// 1. Vite HMR 개발 서버가 구동 중인 경우 감지 및 처리
		if (file_exists($hotPath)) {
			$devServerUrl = trim(file_get_contents($hotPath));
			// HMR 환경에서는 CSS가 JS 내에 자동 삽입되므로 CSS 바인딩 생략
			return $devServerUrl . '/' . $entry;
		}

		// 2. 프로덕션 환경의 manifest.json 읽기
		$manifestPath = __DIR__ . '/../public/dist/.vite/manifest.json';
		if (!file_exists($manifestPath)) {
			$manifestPath = __DIR__ . '/../public/dist/manifest.json';
		}
		
		if (file_exists($manifestPath)) {
			$manifest = json_decode(file_get_contents($manifestPath), true);
			if (isset($manifest[$entry])) {
				$entryData = $manifest[$entry];
				
				// 연관된 CSS 에셋이 존재하는 경우 안전 에셋 로더 경로로 바인딩
				if (is_array($cssArray) && isset($entryData['css']) && is_array($entryData['css'])) {
					foreach ($entryData['css'] as $cssFile) {
						$cssKey = 'vite-' . basename($cssFile, '.css');
						$cssArray[$cssKey] = route('admin_secure_asset', ['path' => $cssFile]);
					}
				}
				
				if (isset($entryData['file'])) {
					return route('admin_secure_asset', ['path' => $entryData['file']]);
				}
			}
		}
		
		// manifest가 존재하지 않는 등의 예외 상황을 대비한 fallback 경로
		return route('admin_secure_asset', ['path' => 'js/app.js']);
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
		'ckeditor' => asset('packages/saaksin/administrator/js/ckeditor/ckeditor.js'),
	);

	// Vite 현대화 에셋 (Alpine.js 및 Tailwind CSS)은 대시보드와 커스텀 페이지를 포함한 모든 레이아웃에 필수적이므로 항상 등록합니다.
	$hotPath = __DIR__ . '/../public/dist/hot';
	if (file_exists($hotPath)) {
		// Vite HMR 활성화 시 @vite/client 추가 로드 및 app.ts HMR 주소 바인딩
		$devServerUrl = trim(file_get_contents($hotPath));
		$view->js['vite-client'] = $devServerUrl . '/@vite/client';
		$view->js['vite-app'] = $devServerUrl . '/resources/js/app.ts';
	} else {
		// 프로덕션 상태에서는 manifest 기반 안전 에셋 로더 적용
		$view->js['vite-app'] = getViteAsset('resources/js/app.ts', $view->css);
	}

    // 3. 사용자 정의 js 추가
    $customs = config('administrator.custom_js');
    if ($customs) $view->js += $customs;
});
