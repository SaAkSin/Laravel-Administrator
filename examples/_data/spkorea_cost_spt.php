<?php
function spkorea_cost_spt()
{
	return array(
		'title' => 'SMALL PACKET WITH TRACKING#',
		'single' => 'Cost',
		'model' => App\Models\SpkoreaCostSPT::class,
		'columns' => array(
			'sct_id' => array(
				'title' => 'ID'
			),
			'sct_weight' => array(
				'title' => 'Weight',
				'output' => function ($value) {
					if (!is_null($value)) {
						return number_format(floatval($value), 2) . ' kg';
					}
					return'';
				}
			),
			'sct_zone' => array(
				'title' => 'Zone'
			),
			'sct_price' => array(
				'title' => 'Price',
				'output' => function ($value) {
					if (!is_null($value)) {
						return number_format(intval($value));
					}
					return '';
				}
			),
			'sct_usd' => array(
				'title' => 'Price(USD)',
				'output' => function ($value) {
					if (!is_null($value)) {
						return '$' . $value;
					}
                    return '';
				}
			),
		),
		'edit_fields' => array(
			'sct_id' => array(
				'title' => 'ID',
				'type' => 'key'
			),
			'sct_weight' => array(
				'title' => 'Weight',
				'type' => 'number',
				'symbol' => 'Kg',
				'decimals' => 1
			),
			'sct_zone' => array(
				'title' => 'Zone',
				'type' => 'text',
				'limit' => 2
			),
			'sct_price' => array(
				'title' => 'Price',
				'type' => 'number',
			),
			'sct_usd' => array(
				'title' => 'Price(USD)',
				'type' => 'number',
				'symbol' => '$',
				'decimals' => 2
			),
		),
		'actions' => array(

		),
		'filters' => array(
			'sct_id' => array(
				'title' => 'ID',
				'type' => 'key'
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
