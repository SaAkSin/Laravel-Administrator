<?php
function spkorea_warehouse_statements()
{
    return array(
        'title' => 'Warehouse Statements',
        'single' => 'Warehouse Statement',
        'model' => App\Models\SpkoreaWarehouseStatement::class,
        'columns' => array(
            'sws_id' => array(
                'title' => 'ID'
            ),
//            'warehouse' => array(
//                'title' => 'Warehouse',
//                'relationship' => 'warehouse',
//                'select' => 'CONCAT((:table).sw_name)'
//            ),
            'supplier' => array(
                'title' => 'Supplier',
                'relationship' => 'supplier',
                'select' => 'CONCAT((:table).ssp_name)'
            ),
            'sws_status' => array(
                'title' => 'Status',
                'output' => function ($value) {
                    switch ($value) {
                        case 'R':
                            return 'ready';
                        case 'C':
                            return 'complete';
                        default:
                            return 'unknown';
                    }
                }
            ),
            'sws_date' => array(
                'title' => 'Date',
                'output' => function($value) {
                    $timezone = new DateTimeZone("Asia/Seoul");
                    $date = new DateTime($value);
                    $date->setTimezone($timezone);
                    return $date->format('Y-m-d H:i:s')." (Asia/Seoul)";
                }
            ),
            'sws_no' => array(
                'title' => 'no'
            ),
        ),
        'edit_fields' => array(
            'sws_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'warehouse' => array(
                'title' => 'Warehouse',
                'type' => 'relationship',
                'name_field' => 'sw_name',
                // sparekorea로 고정
                'value' => 1,
                'editable' => function ($model) {
                    return false;
                }
            ),
            'supplier' => array(
                'title' => 'Supplier',
                'type' => 'relationship',
                'name_field' => 'ssp_name',
            ),
            'sws_status' => array(
                'title' => 'status',
                'type' => 'enum',
                'options' => array(
                    'R' => 'ready',
                    'C' => 'complete'
                )
            ),
            'sws_date' => array(
                'title' => 'date',
                'type' => 'datetime',
            ),
            'sws_no' => array(
                'title' => 'no',
                'type' => 'number',
            ),
            'sws_desc' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'height' => 250
            ),
        ),
        'actions' => array(
        ),
        'filters' => array(
            'sws_id' => array(
                'title' => 'Warehouse Statement ID',
                'type' => 'key'
            ),
            'sws_no' => array(
                'title' => 'no',
                'type' => 'number'
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
