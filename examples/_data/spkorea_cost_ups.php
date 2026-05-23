<?php
function spkorea_cost_ups()
{
    return array(
        'title' => 'UPS',
        'single' => 'Cost',
        'model' => App\Models\SpkoreaCostUPS::class,
        'columns' => array(
            'scu_id' => array(
                'title' => 'ID'
            ),
            'scu_weight' => array(
                'title' => 'Weight',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return number_format(floatval($value), 2) . ' kg';
                    }
                }
            ),
            'scu_zone' => array(
                'title' => 'Zone'
            ),
            'scu_price' => array(
                'title' => 'Price',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return number_format(intval($value));
                    }
                }
            ),
            'scu_usd' => array(
                'title' => 'Price(USD)',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return '$' . $value;
                    }
                }
            ),
        ),
        'edit_fields' => array(
            'scu_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'scu_weight' => array(
                'title' => 'Weight',
                'type' => 'number',
                'symbol' => 'Kg',
                'decimals' => 1
            ),
            'scu_zone' => array(
                'title' => 'Zone',
                'type' => 'text',
                'limit' => 2
            ),
            'scu_price' => array(
                'title' => 'Price',
                'type' => 'number',
            ),
            'scu_usd' => array(
                'title' => 'Price(USD)',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
        ),
        'actions' => array(

        ),
        'filters' => array(
            'scu_id' => array(
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
