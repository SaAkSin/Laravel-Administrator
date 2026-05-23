<?php

/**
 * Post 모델의 관리자 설정 파일입니다.
 */
function posts()
{
	return array(
		'title' => 'Posts',
		'single' => 'post',
		'model' => 'App\Models\Post',
		'form_width' => 500,

		'columns' => array(
			'id' => array(
				'title' => 'ID',
			),
			'title' => array(
				'title' => 'Title',
			),
			'user' => array(
				'title' => 'Author',
				'relationship' => 'user',
				'select' => '(:table).name',
			),
			'created_at' => array(
				'title' => 'Created At',
			),
		),

		'filters' => array(
			'id' => array(
				'title' => 'ID',
				'type' => 'key',
			),
			'title' => array(
				'title' => 'Title',
				'type' => 'text',
			),
			'user' => array(
				'title' => 'Author',
				'type' => 'relationship',
				'name_field' => 'name',
			),
		),

		'edit_fields' => array(
			'id' => array(
				'title' => 'ID',
				'type' => 'key',
			),
			'user' => array(
				'title' => 'Author (BelongsTo)',
				'type' => 'relationship',
				'name_field' => 'name',
			),
			'title' => array(
				'title' => 'Title',
				'type' => 'text',
			),
			'content' => array(
				'title' => 'Content',
				'type' => 'textarea',
				'height' => 150,
			),
		),
	);
}
