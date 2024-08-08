<?php

use Illuminate\Support\Facades\Route;

/**
 * Temperary solution for middleware in routes
 * TODO: remove in favor of setting the config for middleware outside of the routes file
 */
$middleware_array = array('SaAkSin\Administrator\Http\Middleware\ValidateAdmin');
if(is_array(config('administrator.middleware'))) {
    $middleware_array = array_merge(config('administrator.middleware'), $middleware_array);
}

/**
 * Routes
 */
Route::group(array('domain' => config('administrator.domain'), 'prefix' => config('administrator.uri'), 'middleware' => $middleware_array), function()
{
	//Admin Dashboard
    Route::get('/', [\SaAkSin\Administrator\AdminController::class, 'dashboard'])->name('admin_dashboard');

	//File Downloads
    Route::get('file_download', [\SaAkSin\Administrator\AdminController::class, 'fileDownload'])->name('admin_file_download');


	//Custom Pages
    Route::get('page/{page}', [\SaAkSin\Administrator\AdminController::class, 'page'])->name('admin_page');

	Route::group(array('middleware' => ['SaAkSin\Administrator\Http\Middleware\ValidateSettings', 'SaAkSin\Administrator\Http\Middleware\PostValidate']), function()
	{
		//Settings Pages
        Route::get('settings/{settings}', [\SaAkSin\Administrator\AdminController::class, 'settings'])->name('admin_settings');

		//Display a settings file
        Route::get('settings/{settings}/file', [\SaAkSin\Administrator\AdminController::class, 'displayFile'])->name('admin_settings_display_file');

		//Save Item
        Route::post('settings/{settings}/save', [\SaAkSin\Administrator\AdminController::class, 'settingsSave'])->name('admin_settings_save');

		//Custom Action
        Route::post('settings/{settings}/custom_action', [\SaAkSin\Administrator\AdminController::class, 'settingsCustomAction'])->name('admin_settings_custom_action');

		//Settings file upload
        Route::post('settings/{settings}/{field}/file_upload', [\SaAkSin\Administrator\AdminController::class, 'fileUpload'])->name('admin_settings_file_upload');
	});

	//Switch locales
    Route::get('switch_locale/{locale}', [\SaAkSin\Administrator\AdminController::class, 'switchLocale'])->name('admin_switch_locale');

	//The route group for all other requests needs to validate admin, model, and add assets
	Route::group(array('middleware' => ['SaAkSin\Administrator\Http\Middleware\ValidateModel', 'SaAkSin\Administrator\Http\Middleware\PostValidate']), function()
	{
		//Model Index
        Route::get('{model}', [\SaAkSin\Administrator\AdminController::class, 'index'])->name('admin_index');

		//New Item
        Route::get('{model}/new', [\SaAkSin\Administrator\AdminController::class, 'item'])->name('admin_new_item');

		//Update a relationship's items with constraints
        Route::post('{model}/update_options', [\SaAkSin\Administrator\AdminController::class, 'updateOptions'])->name('admin_update_options');

		//Display an image or file field's image or file
        Route::get('{model}/file', [\SaAkSin\Administrator\AdminController::class, 'displayFile'])->name('admin_display_file');

		//Updating Rows Per Page
        Route::post('{model}/rows_per_page', [\SaAkSin\Administrator\AdminController::class, 'rowsPerPage'])->name('admin_rows_per_page');

		//Get results
        Route::post('{model}/results', [\SaAkSin\Administrator\AdminController::class, 'results'])->name('admin_get_results');

		//Custom Model Action
        Route::post('{model}/custom_action', [\SaAkSin\Administrator\AdminController::class, 'customModelAction'])->name('admin_custom_model_action');

		//Get Item
        Route::get('{model}/{id}', [\SaAkSin\Administrator\AdminController::class, 'item'])->name('admin_get_item');

		//File Uploads
        Route::post('{model}/{field}/file_upload', [\SaAkSin\Administrator\AdminController::class, 'fileUpload'])->name('admin_file_upload');

		//Save Item
        Route::post('{model}/{id?}/save', [\SaAkSin\Administrator\AdminController::class, 'save'])->name('admin_save_item');

		//Delete Item
        Route::post('{model}/{id}/delete', [\SaAkSin\Administrator\AdminController::class, 'delete'])->name('admin_delete_item');

		//Custom Item Action
        Route::post('{model}/{id}/custom_action', [\SaAkSin\Administrator\AdminController::class, 'customModelItemAction'])->name('admin_custom_model_item_action');
	});
});
