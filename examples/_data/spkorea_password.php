<?php
function spkorea_password()
{
	return array(
		'title' => 'Member\'s password',
		'single' => 'Member',
		'model' => App\Models\SpkoreaUser::class,
		'columns' => array(
			'su_id' => array(
				'title' => 'ID'
			),
			'su_profile' => array(
				'title' => 'photo',
				'output' => function ($value) {
					if ($value != '') {
						if (strpos($value, 'ttp://') || strpos($value, 'ttps://')) {
							return '<center><img src="' . $value . '" height="50"></center>';
						} else {
							return '<center><img src="/img/photos/thumbs/' . $value . '" height="50"></center>';
						}
					} else {
						return '<center>no photos</center>';
					}
				}
			),
			'su_uid' => array(
				'title' => 'SNS ID'
			),
			'email' => array(
				'title' => 'e-mail'
			),
			'su_name' => array(
				'title' => 'name'
			),
			'su_gender' => array(
				'title' => 'gender'
			),
			'su_phone' => array(
				'title' => 'phone'
			),
			'su_mailing' => array(
				'title' => 'mailing',
				'output' => function ($value) {
					if ($value) {
						return 'Yes';
					} else {
						return 'No';
					}
				}
			),
			'su_type' => array(
				'title' => 'join',
				'output' => function ($value) {
					switch ($value) {
						case 'S1':
							return 'Sparekorea';
						case 'F1':
							return 'Facebook';
						case 'T1':
							return 'Twitter';
						case 'G1':
							return 'Google';
						case 'P1':
							return 'Paypal';
						default:
							return 'unknown';
					}
				}
			),
			'su_status' => array(
				'title' => 'status',
				'output' => function ($value) {
					switch ($value) {
						case 'N':
							return 'in activation';
						case 'C':
							return 'activated';
						case 'S':
							return 'disabled';

						default:
							return 'unknown';
					}
				}
			),
		),
		'edit_fields' => array(
			'su_id' => array(
				'title' => 'ID',
				'type' => 'key'
			),
			'su_uid' => array(
				'title' => 'SNS ID',
				'description' => 'twitter\'s unique id',
				'editable' => false
			),
			'email' => array(
				'title' => 'e-mail',
				'type' => 'text',
				'limit' => 100,
				'editable' => false
			),
			'password' => array(
				'title' => 'password',
				'type' => 'password',
				'limit' => 20
			),
			'su_name' => array(
				'title' => 'name',
				'type' => 'text',
				'limit' => 50,
				'editable' => false
			),
			'su_phone' => array(
				'title' => 'phone',
				'type' => 'text',
				'limit' => 25,
				'editable' => false
			),
			'su_profile' => array(
				'title' => 'Photo (1M limit)',
				'type' => 'image',
				'location' => public_path() . '/img/photos/',
				'naming' => 'random',
				'length' => 20,
				'size_limit' => 1,
				'sizes' => array(
					array(150, 150, 'auto', public_path() . '/img/photos/thumbs/', 100)
				),
				'visible' => function ($model) {
					return $model->su_type == 'S1';
				},
				'editable' => false
			),
			'su_desc' => array(
				'title' => 'profile',
				'type' => 'textarea',
				'height' => 250,
				'editable' => false
			)
		),
		'actions' => array(
			'view_member' => array(
				'title' => 'Go to Members',
				'action' => function ($model) {
					return Redirect::to('/admin/spkorea_users/' . $model->su_id);
				}
			),
		),
		'action_permissions' => array(
			'create' => false,
		),
		'filters' => array(
			'su_id' => array(
				'title' => 'User ID',
				'type' => 'key'
			),
			'email' => array(
				'title' => 'e-mail',
				'type' => 'text'
			),
			'su_name' => array(
				'title' => 'name',
				'type' => 'text'
			),
			'su_phone' => array(
				'title' => 'phone',
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
