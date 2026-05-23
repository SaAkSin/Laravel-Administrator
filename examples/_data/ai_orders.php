<?php

function ai_orders()
{
    return array(
        'title' => 'AI Order Verifications',
        'single' => 'AI Order Verification',
        'model' => App\Models\AIOrder::class,
        'columns' => array(
            'ao_id' => array(
                'title' => 'ID',
            ),
            'created_at' => array(
                'title' => 'Date',
                'output' => function($value) {
                    $timezone = new DateTimeZone("Asia/Seoul");
                    $date = new DateTime($value);
                    $date->setTimezone($timezone);
                    return $date->format('Y-m-d H:i:s')." (Asia/Seoul)";
                }
            ),
            'order_id' => array(
                'title' => 'Order ID',
                'relationship' => 'order',
                'select' => 'CONCAT((:table).so_id, " -  ", (:table).so_name)'
            ),
            'ao_type' => array(
                'title' => 'AI',
                'output' => function($value) {
                    switch ($value) {
                        case 'O':
                            return 'Ollama';
                        case 'G':
                            return 'Google';
                        default:
                            return 'Unknown';
                    }
                }

            ),
            'ao_model' => array(
                'title' => 'Model',
            ),
            'ao_result' => array(
                'title' => 'Result',
                'output' => function($value) {
                    switch ($value) {
                        case 'S':
                            return 'Success';
                        default:
                            return 'Failed';
                    }
                }
            ),
            'ao_quantity' => array(
                'title' => 'Quantity',
            ),
            'ao_carrier' => array(
                'title' => 'Carrier',
            ),
            'ao_weight' => array(
                'title' => 'Weight',
                'output' => function($value) {
                    return $value . ' kg';
                }
            ),
            'ao_price' => array(
                'title' => 'Shipping Price',
                'output' => function($value) {
                    return '$' . number_format($value, 2);
                }
            )
        ),
        'edit_fields' => array(
            'ao_id' => array(
                'title' => 'ID',
                'type' => 'key',
            ),
            'ao_quantity' => array(
                'title' => 'Quantity',
                'editable' => false,
            ),
            'ao_carrier' => array(
                'title' => 'Carrier',
                'editable' => false,
            ),
            'ao_weight' => array(
                'title' => 'Weight',
                'symbol' => 'kg',
                'editable' => false,
            ),
            'ao_price' => array(
                'title' => 'Price',
                'symbol' => '$',
                'editable' => false,
            ),
            'ao_response' => array(
                'title' => 'Response',
                'type' => 'textarea',
                'editable' => false,
            ),
            'ao_memo' => array(
                'title' => 'Message',
                'type' => 'textarea',
                'editable' => false,
            ),
        ),
        'filters' => array(
            'ao_result' => array(
                'title' => 'RESULT',
                'type' => 'enum',
                'options' => array(
                    'S' => 'SUCCESS',
                    'F' => 'FAIL',
                )
            ),
        ),
        'action_permissions'=> array(
            'create' => function($model)
            {
                return false;
            },
            'update' => function($model)
            {
                return false;
            },
            'delete' => function($model)
            {
                return false;
            }
        ),
        'form_width' => 450,
    );
}
