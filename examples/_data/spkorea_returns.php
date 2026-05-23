<?php
function spkorea_returns()
{
    return array(
        'title' => 'Returns',
        'single' => 'Return',
        'model' => App\Models\SpkoreaReturn::class,
        'columns' => array(
            'se_id' => array(
                'title' => 'ID'
            ),
            'created_at' => array(
                'title' => 'Date',
            ),
            'se_status' => array(
                'title' => 'Status',
                'output' => function ($value) {
                    switch ($value) {
                        case 'R':
                            return 'Receipt';
                        case 'C':
                            return 'Complete';
                        default:
                            return 'N/A';
                    }
                }
            ),
            'user' => array(
                'title' => 'Order of',
                'relationship' => 'order.user',
                'select' => 'CONCAT((:table).su_name, " - ", (:table).email)'
            ),
            'order' => array(
                'title' => 'Order',
                'relationship' => 'order',
                'select' => '(:table).so_desc'
            ),
            'se_desc' => array(
                'title' => 'Return Reason'
            )
        ),
        'edit_fields' => array(
            'se_id' => array(
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
                'editable' => function ($model) {
                    return !$model->exists;
                }
            ),
            'se_status' => array(
                'title' => 'Status',
                'type' => 'enum',
                'options' => array(
                    'R' => 'Receipt',
                    'C' => 'Complete',
                )
            ),
            'se_desc' => array(
                'title' => 'Message',
                'type' => 'textarea',
                'height' => 130
            ),
            'se_memo' => array(
                'title' => 'Memo',
                'type' => 'textarea',
                'height' => 130
            )


        ),
        'actions' => array(),
        'filters' => array(
            'se_id' => array(
                'title' => 'Order ID',
                'type' => 'key'
            ),
//        'order' => array(
//            'title' => 'Order',
//            'type' => 'text'
//        ),

        ),
        'action_permissions' => array(
            'delete' => function ($model) {
                return false;
            }
        ),
        'form_width' => 450
    );
}
