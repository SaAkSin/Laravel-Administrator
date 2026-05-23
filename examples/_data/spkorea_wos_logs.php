<?php
function spkorea_wos_logs()
{
	return array(
		'title' => 'Logs',
		'single' => 'Log',
		'model' => App\Models\SpkoreaWosLog::class,
		'columns' => array(
			'swl_id' => array(
				'title' => 'ID'
			),
			'swl_table' => array(
				'title' => 'table'
			),
            'swl_key' => array(
                'title' => 'key'
            ),
            'swl_account' => array(
                'title' => 'account'
            ),
            'swl_name' => array(
                'title' => 'name'
            ),
            'swl_crud' => array(
                'title' => 'CRUD'
            ),
            'created_at' => array(
                'title' => 'datetime'
            ),
		),
		'edit_fields' => array(
			'swl_id' => array(
				'title' => 'ID',
				'type' => 'key'
			),
            'swl_table' => array(
                'title' => 'table',
                'type' => 'text',
                'limit' => 100
            ),
            'swl_key' => array(
                'title' => 'key',
                'type' => 'number',
            ),
            'swl_account' => array(
                'title' => 'account',
                'type' => 'text',
                'limit' => 50
            ),
            'swl_name' => array(
                'title' => 'name',
                'type' => 'text',
                'limit' => 30
            ),
            'swl_crud' => array(
                'title' => 'CRUD',
                'type' => 'text',
                'limit' => 1
            ),
			'swl_desc' => array(
				'title' => 'Description',
				'type' => 'textarea',
				'height' => 250
			),
		),
		'actions' => array(
//			'view_password' => array(
//				'title' => 'Change password',
//				'action' => function ($model) {
//					return Redirect::to('/admin/spkorea_password/' . $model->su_id);
//				}
//			),
		),
		'filters' => array(
			'swl_id' => array(
				'title' => 'User ID',
				'type' => 'key'
			),
            'swl_table' => array(
                'title' => 'table',
                'type' => 'text'
            ),
            'swl_key' => array(
                'title' => 'key',
                'type' => 'number'
            ),
            'swl_account' => array(
                'title' => 'account',
                'type' => 'text'
            ),
		),
        'permission'=> function()
        {
            $user = auth()->user();
            if ($user) {
                return !$user->isSubAdmin();
            }
            return false;
        },
		'form_width' => 450
	);
}
