<?php
function spkorea_users()
{
	return array(
		'title' => 'Members',
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
				'title' => 'name',
                'output' => function ($value, $model) {
                    $result = $model->orders()
                        ->where('so_status', 'S')
                        ->selectRaw('count(*) as count, sum(so_price) as total_price')
                        ->first();
                    $count = $result->count;
                    $sum = $result->total_price ?? 0; // 결과가 없을 경우 null 방지
                    return $value.'<br>'.$count.' / $'.floor($sum*100)/100;
                }
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
            'grade' => array(
                'title' => 'Grade',
                'relationship' => 'grade',
                'select' => 'CONCAT((:table).sug_grade, " -  ", (:table).sug_discount, " %")'
            ),
            'su_excluded' => array(
                'title' => 'Shipping Ex',
                'output' => function ($value) {
                    if ($value) {
                        return 'Free';
                    } else {
                        return '';
                    }
                }
            )
		),
		'edit_fields' => array(
			'su_id' => array(
				'title' => 'ID',
				'type' => 'key'
			),
			'su_uid' => array(
				'title' => 'SNS ID',
				'description' => 'twitter\'s unique id',
				'editable' => function ($model) {
					return false;
				}
			),
			'email' => array(
				'title' => 'e-mail',
				'type' => 'text',
				'limit' => 100
			),
			'su_name' => array(
				'title' => 'name',
				'type' => 'text',
				'limit' => 50
			),
            'grade' => array(
                'title' => 'Grade',
                'type' => 'relationship',
                'name_field' => 'sug_grade',
            ),
            'su_excluded' => array(
                'title' => 'Shipping Excluded',
                'type' => 'bool',
            ),
			'su_gender' => array(
				'title' => 'gender',
				'type' => 'enum',
				'options' => array(
					'M' => 'male',
					'F' => 'female'
				)
			),
			'su_birth' => array(
				'title' => 'birth',
				'type' => 'date',
				'date_format' => 'yy-mm-dd',
				'description' => 'ex: 2015-01-01'
			),
			'su_phone' => array(
				'title' => 'phone',
				'type' => 'text',
				'limit' => 25,
			),
			'su_mailing' => array(
				'title' => 'mailing',
				'type' => 'bool'
			),
			'su_type' => array(
				'title' => 'join',
				'type' => 'enum',
				'options' => array(
					'S1' => 'Sparekorea',
					'F1' => 'Facebook',
					'T1' => 'Twitter',
					'G1' => 'Google',
					'P1' => 'Paypal'
				)
			),
			'su_status' => array(
				'title' => 'status',
				'type' => 'enum',
				'options' => array(
					'N' => 'in activation',
					'C' => 'activated',
					'S' => 'disabled'
				)
			),
            'su_option' => array(
                'title' => 'use drop shipping option',
                'type' => 'bool'
            ),
            'su_shipper' => array(
                'title' => 'Shipper',
                'type' => 'text',
                'limit' => 100
            ),
			'su_admin' => array(
				'title' => 'admin',
				'type' => 'bool'
			),
			'su_subadmin' => array(
				'title' => 'subadmin',
				'type' => 'bool'
			),
			'su_profile' => array(
				'title' => 'Photo (1M limit)',
				'type' => 'image',
				'location' => public_path().'/img/photos/',
				'naming' => 'random',
				'length' => 20,
				'size_limit' => 1,
				'sizes' => array(
					array(150, 150, 'auto', public_path() . '/img/photos/thumbs/', 100)
				),
				'visible' => function ($model) {
					return $model->su_type == 'S1';
				}
			),
			'su_desc' => array(
				'title' => 'profile',
				'type' => 'textarea',
				'height' => 250
			)
		),
		'actions' => array(
			'view_password' => array(
				'title' => 'Change password',
				'action' => function ($model) {
					return Redirect::to('/admin/spkorea_password/' . $model->su_id);
				}
			),
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
