<?php
function spkorea_cancels()
{
    return array(
        'title' => 'Cancels',
        'single' => 'Cancel',
        'model' => App\Models\SpkoreaCancel::class,
        'columns' => array(
            'sl_id' => array(
                'title' => 'ID'
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
            'sl_status' => array(
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
            'sl_desc' => array(
                'title' => 'Cancel Reason'
            )
        ),
        'edit_fields' => array(
            'sl_id' => array(
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
            'sl_status' => array(
                'title' => 'Status',
                'type' => 'enum',
                'options' => array(
                    'R' => 'Receipt',
                    'C' => 'Complete',
                )
            ),
            'sl_desc' => array(
                'title' => 'Message',
                'type' => 'textarea',
                'height' => 130
            ),
            'sl_memo' => array(
                'title' => 'Memo',
                'type' => 'textarea',
                'height' => 130
            )


        ),
        'actions' => array(
            'cancel_order' => array(
                'title' => 'Cancel Submit',
                'messages' => array(
                    'active' => 'Processing...',
                    'success' => 'Success.',
                    'error' => 'Failed.'
                ),
                'confirmation' => 'Are you sure?',
                'permission' => function($model)
                {
                    // 접수 상태인 경우에만 허용
                    return $model->sl_status == 'R';
                },
                'action' => function($model)
                {
                    return $model->cancelOrder();
                }
            ),
        ),
        'filters' => array(
            'sl_id' => array(
                'title' => 'Cancel ID',
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