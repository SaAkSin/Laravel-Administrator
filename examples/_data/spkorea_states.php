<?php
function spkorea_states()
{
	return array(
		'title' => 'States',
		'single' => 'States',
		'model' => App\Models\SpkoreaState::class,
		'columns' => array(
			'st_id' => array(
				'title' => 'ID'
			),
			'st_city' => array(
				'title' => 'Country'
			),
		),
		'edit_fields' => array(
			'st_id' => array(
				'title' => 'ID',
				'type' => 'key'
			),
			'country' => array(
				'title' => 'Country',
				'type' => 'relationship',
				'name_field' => 'sc_country'
			),
			'st_city' => array(
				'title' => 'State or City',
				'type' => 'text',
				'limit' => 100
			),
		),
		'actions' => array(
			// 'view_messages' => array(
			// 	'title' => '메시지 보기',
			// 	'action' => function($model)
			// 	{
			// 		Session::put('users_id', $model->du_id);
			// 		return Redirect::to('/admin/doodleit_messages');
			// 	}
			// ),
		),
		'filters' => array(
			'sc_id' => array(
				'title' => 'Country ID',
				'type' => 'key'
			),
			'sc_country' => array(
				'title' => 'Country',
				'type' => 'text'
			),
			'sc_code' => array(
				'title' => 'Country Code',
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
