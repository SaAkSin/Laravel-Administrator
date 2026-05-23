<?php

/**
 * Role 모델의 관리자 설정 파일입니다.
 */
function roles()
{
	return array(
		'title' => 'Roles',
		'single' => 'role',
		'model' => 'App\Models\Role',
		'form_width' => 400,

		'columns' => array(
			'id' => array(
				'title' => 'ID',
			),
			'name' => array(
				'title' => 'Role Name',
			),
		),

		'filters' => array(
			'id' => array(
				'title' => 'ID',
				'type' => 'key',
			),
			'name' => array(
				'title' => 'Role Name',
				'type' => 'text',
			),
		),

		'edit_fields' => array(
			'id' => array(
				'title' => 'ID',
				'type' => 'key',
			),
			'name' => array(
				'title' => 'Role Name',
				'type' => 'text',
			),
		),
	);
}
