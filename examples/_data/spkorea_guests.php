<?php
function spkorea_guests()
{
	return array(
		'title' => 'Guests',
		'single' => 'Guest',
		'model' => App\Models\SpkoreaGuest::class,
		'columns' => array(
			'sgt_id' => array(
				'title' => 'ID'
			),
			'sgt_email' => array(
				'title' => 'e-mail'
			),
			'sgt_name' => array(
				'title' => 'name'
			),
		),
		'edit_fields' => array(
			'sgt_id' => array(
				'title' => 'ID',
				'type' => 'key'
			),
			'sgt_email' => array(
				'title' => 'e-mail',
				'type' => 'text',
				'limit' => 100
			),
            'sgt_password' => array(
                'title' => 'password',
                'type' => 'text',
                'limit' => 50
            ),
            'sgt_name' => array(
                'title' => 'name',
                'type' => 'text',
                'limit' => 50
            ),
		),
		'actions' => array(
		),
		'filters' => array(
			'sgt_id' => array(
				'title' => 'Guest ID',
				'type' => 'key'
			),
			'sgt_email' => array(
				'title' => 'e-mail',
				'type' => 'text'
			),
			'sgt_name' => array(
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
