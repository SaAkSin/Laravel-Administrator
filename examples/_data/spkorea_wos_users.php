<?php
function spkorea_wos_users()
{
	return array(
		'title' => 'Users',
		'single' => 'User',
		'model' => App\Models\SpkoreaWosUser::class,
		'columns' => array(
			'swu_id' => array(
				'title' => 'ID'
			),
			'swu_account' => array(
				'title' => 'account'
			),
			'swu_name' => array(
				'title' => 'name'
			),
			'swu_status' => array(
				'title' => 'Status',
				'output' => function ($value) {
					switch ($value) {
						case 'A':
							return 'activated';
						case 'D':
							return 'disabled';
						default:
							return 'unknown';
					}
				}
			),
		),
		'edit_fields' => array(
			'swu_id' => array(
				'title' => 'ID',
				'type' => 'key'
			),
			'swu_account' => array(
				'title' => 'account',
				'type' => 'text',
				'limit' => 50
			),
			'swu_name' => array(
				'title' => 'name',
				'type' => 'text',
				'limit' => 30
			),
            'swu_password' => array(
                'title' => 'password',
                'type' => 'password',
                'limit' => 20
            ),
			'swu_status' => array(
				'title' => 'status',
				'type' => 'enum',
				'options' => array(
					'A' => 'activated',
					'D' => 'disabled'
				),
                'value' => 'A',
			),
			'swu_desc' => array(
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
			'swu_id' => array(
				'title' => 'User ID',
				'type' => 'key'
			),
			'swu_account' => array(
				'title' => 'account',
				'type' => 'text'
			),
			'swu_name' => array(
				'title' => 'name',
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
