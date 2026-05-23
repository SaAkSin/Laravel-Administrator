<?php
function spkorea_cost_fedex()
{
    return array(
        'title' => 'FEDEX',
        'single' => 'Cost',
        'model' => App\Models\SpkoreaCostFEDEX::class,
        'columns' => array(
            'scf_id' => array(
                'title' => 'ID'
            ),
            'scf_weight' => array(
                'title' => 'Weight',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return number_format(floatval($value), 2) . ' kg';
                    }
                }
            ),
            'scf_zone' => array(
                'title' => 'Zone'
            ),
            'scf_price' => array(
                'title' => 'Price',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return number_format(intval($value));
                    }
                }
            ),
            'scf_usd' => array(
                'title' => 'Price(USD)',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return '$' . $value;
                    }
                }
            ),
        ),
        'edit_fields' => array(
            'scf_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'scf_weight' => array(
                'title' => 'Weight',
                'type' => 'number',
                'symbol' => 'Kg',
                'decimals' => 1
            ),
            'scf_zone' => array(
                'title' => 'Zone',
                'type' => 'text',
                'limit' => 2
            ),
            'scf_price' => array(
                'title' => 'Price',
                'type' => 'number',
            ),
            'scf_usd' => array(
                'title' => 'Price(USD)',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
        ),
        'actions' => array(

        ),
        'filters' => array(
            'scf_id' => array(
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
