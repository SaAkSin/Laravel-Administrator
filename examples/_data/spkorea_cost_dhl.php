<?php
function spkorea_cost_dhl()
{
    return array(
        'title' => 'DHL',
        'single' => 'Cost',
        'model' => App\Models\SpkoreaCostDHL::class,
        'columns' => array(
            'scd_id' => array(
                'title' => 'ID'
            ),
            'scd_weight' => array(
                'title' => 'Weight',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return number_format(floatval($value), 2) . ' kg';
                    }
                }
            ),
            'scd_zone' => array(
                'title' => 'Zone'
            ),
            'scd_price' => array(
                'title' => 'Price',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return number_format(intval($value));
                    }
                }
            ),
            'scd_usd' => array(
                'title' => 'Price(USD)',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return '$' . $value;
                    }
                }
            ),
        ),
        'edit_fields' => array(
            'scd_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'scd_weight' => array(
                'title' => 'Weight',
                'type' => 'number',
                'symbol' => 'Kg',
                'decimals' => 1
            ),
            'scd_zone' => array(
                'title' => 'Zone',
                'type' => 'text',
                'limit' => 2
            ),
            'scd_price' => array(
                'title' => 'Price',
                'type' => 'number',
            ),
            'scd_usd' => array(
                'title' => 'Price(USD)',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
        ),
        'actions' => array(

        ),
        'filters' => array(
            'scd_id' => array(
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
