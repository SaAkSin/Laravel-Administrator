<?php
function spkorea_cost_op()
{
    return array(
        'title' => 'Ocean Post',
        'single' => 'Cost',
        'model' => App\Models\SpkoreaCostOP::class,
        'columns' => array(
            'sco_id' => array(
                'title' => 'ID'
            ),
            'sco_weight' => array(
                'title' => 'Weight',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return number_format(floatval($value), 2) . ' kg';
                    }
                }
            ),
            'sco_zone' => array(
                'title' => 'Zone'
            ),
            'sco_price' => array(
                'title' => 'Price',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return number_format(intval($value));
                    }
                }
            ),
            'sco_usd' => array(
                'title' => 'Price(USD)',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return '$' . $value;
                    }
                }
            ),
        ),
        'edit_fields' => array(
            'sco_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'sco_weight' => array(
                'title' => 'Weight',
                'type' => 'number',
                'symbol' => 'Kg',
                'decimals' => 1
            ),
            'sco_zone' => array(
                'title' => 'Zone',
                'type' => 'text',
                'limit' => 2
            ),
            'sco_price' => array(
                'title' => 'Price',
                'type' => 'number',
            ),
            'sco_usd' => array(
                'title' => 'Price(USD)',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
        ),
        'actions' => array(

        ),
        'filters' => array(
            'sco_id' => array(
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
