<?php
function spkorea_forwarding_statements()
{
    return array(
        'title' => 'Forwarding Statements',
        'single' => 'Forwarding Statement',
        'model' => App\Models\SpkoreaForwardingStatement::class,
        'columns' => array(
            'sfs_id' => array(
                'title' => 'ID'
            ),
            'order' => array(
                'title' => 'Order',
                'relationship' => 'order',
                'select' => '(:table).so_desc'
            ),
            'sfs_type' => array(
                'title' => 'Type',
                'output' => function ($value) {
                    switch ($value) {
                        case 'S':
                            return 'Sparekorea';
                        case 'E':
                            return 'eBay';
                        default:
                            return 'unknown';
                    }
                }
            ),
            'sfs_status' => array(
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
            'sfs_date' => array(
                'title' => 'Date',
                'output' => function($value) {
                    $timezone = new DateTimeZone("Asia/Seoul");
                    $date = new DateTime($value);
                    $date->setTimezone($timezone);
                    return $date->format('Y-m-d H:i:s')." (Asia/Seoul)";
                }
            ),
            'sfs_no' => array(
                'title' => 'no'
            ),
        ),
        'edit_fields' => array(
            'sfs_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'order' => array(
                'title' => 'Order',
                'type' => 'relationship',
                'name_field' => 'so_desc',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('CONCAT(so_id, " - ", so_desc)'),
//                'editable' => function ($model) {
//                    return !$model->exists;
//                }
            ),
            'sfs_type' => array(
                'title' => 'type',
                'type' => 'enum',
                'options' => array(
                    'S' => 'Sparekorea',
                    'E' => 'eBay'
                ),
                'value' => 'S'
            ),
            'sfs_status' => array(
                'title' => 'status',
                'type' => 'enum',
                'options' => array(
                    'R' => 'ready',
                    'C' => 'complete'
                )
            ),
            'sfs_date' => array(
                'title' => 'date',
                'type' => 'datetime',
            ),
            'sfs_no' => array(
                'title' => 'no',
                'type' => 'number',
            ),
            'sfs_desc' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'height' => 250
            ),
        ),
        'actions' => array(
        ),
        'filters' => array(
            'sfs_id' => array(
                'title' => 'Forwarding Statement ID',
                'type' => 'key'
            ),
            'sfs_no' => array(
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
