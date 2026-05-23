<?php
function spkorea_cost_ems()
{
    return array(
        'title' => 'EMS',
        'single' => 'Cost',
        'model' => App\Models\SpkoreaCostEMS::class,
        'columns' => array(
            'sce_id' => array(
                'title' => 'ID'
            ),
            'sce_weight' => array(
                'title' => 'Weight',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return number_format(floatval($value), 2) . ' kg';
                    }
                }
            ),
            'sce_zone' => array(
                'title' => 'Zone'
            ),
            'sce_price' => array(
                'title' => 'Price',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return number_format(intval($value));
                    }
                }
            ),
            'sce_usd' => array(
                'title' => 'Price(USD)',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return '$' . $value;
                    }
                }
            ),
        ),
        'edit_fields' => array(
            'sce_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'sce_weight' => array(
                'title' => 'Weight',
                'type' => 'number',
                'symbol' => 'Kg',
                'decimals' => 1
            ),
            'sce_zone' => array(
                'title' => 'Zone',
                'type' => 'text',
                'limit' => 2
            ),
            'sce_price' => array(
                'title' => 'Price',
                'type' => 'number',
            ),
            'sce_usd' => array(
                'title' => 'Price(USD)',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
        ),
        'actions' => array(

        ),
        'filters' => array(
            'sce_id' => array(
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
