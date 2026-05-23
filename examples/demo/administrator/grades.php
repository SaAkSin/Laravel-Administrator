<?php

/**
 * Grade 모델의 관리자 설정 파일입니다.
 */
function grades()
{
	return array(
		'title' => 'Grades',
		'single' => 'grade',
		'model' => 'App\Models\Grade',
		'form_width' => 400,

		'columns' => array(
			'id' => array(
				'title' => 'ID',
			),
			'name' => array(
				'title' => 'Grade Name',
			),
			'discount' => array(
				'title' => 'Discount Rate (%)',
				'output' => function ($value) {
					return $value . '%';
				}
			),
		),

		'filters' => array(
			'id' => array(
				'title' => 'ID',
				'type' => 'key',
			),
			'name' => array(
				'title' => 'Grade Name',
				'type' => 'text',
			),
		),

		'edit_fields' => array(
			'id' => array(
				'title' => 'ID',
				'type' => 'key',
			),
			'name' => array(
				'title' => 'Grade Name',
				'type' => 'text',
			),
			'discount' => array(
				'title' => 'Discount Rate (%)',
				'type' => 'number',
			),
		),
	);
}
