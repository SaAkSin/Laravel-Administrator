<?php
function spkorea_cost_sp()
{
    return array(
        'title' => 'SMALL PACKET',
        'single' => 'Cost',
        'model' => App\Models\SpkoreaCostSP::class,
        'columns' => array(
            'scs_id' => array(
                'title' => 'ID'
            ),
            'scs_weight' => array(
                'title' => 'Weight',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return number_format(floatval($value), 2) . ' kg';
                    }
                    return '';
                }
            ),
            'scs_zone' => array(
                'title' => 'Zone'
            ),
            'scs_price' => array(
                'title' => 'Price',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return number_format(intval($value));
                    }
                    return '';
                }
            ),
            'scs_usd' => array(
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
            'scs_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'scs_weight' => array(
                'title' => 'Weight',
                'type' => 'number',
                'symbol' => 'Kg',
                'decimals' => 1
            ),
            'scs_zone' => array(
                'title' => 'Zone',
                'type' => 'text',
                'limit' => 2
            ),
            'scs_price' => array(
                'title' => 'Price',
                'type' => 'number',
            ),
            'scs_usd' => array(
                'title' => 'Price(USD)',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
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
            'scs_id' => array(
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
