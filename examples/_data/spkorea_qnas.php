<?php
function spkorea_qnas()
{
    $so_id = Session::get('orders_id');

    if ($so_id) {
        $order = App\Models\SpkoreaOrder::find($so_id);
        $transaction =  strval($so_id).' - '.$order->so_code;
        return array(
            'title' => 'Q&A ('.$transaction. ')',
            'single' => 'Q&A',
            'model' => App\Models\SpkoreaQnA::class,
            'columns' => array(
                'sq_id' => array(
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
                'sq_status' => array(
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
                'sq_question' => array(
                    'title' => 'Question'
                )
            ),
            'edit_fields' => array(
                'sq_id' => array(
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
                'sq_status' => array(
                    'title' => 'Status',
                    'type' => 'enum',
                    'options' => array(
                        'R' => 'Receipt',
                        'C' => 'Complete',
                    )
                ),
                'sq_question' => array(
                    'title' => 'Question',
                    'type' => 'textarea',
                    'height' => 130
                ),
                'sq_answer' => array(
                    'title' => 'Answer',
                    'type' => 'textarea',
                    'height' => 130
                )


            ),
            'actions' => array(),
            'filters' => array(
                'so_id' => array(
                    'title' => 'Order ID',
                    'type' => 'key',
                    'value' => $so_id
                ),
                'sq_status' => array(
                    'type' => 'enum',
                    'title' => 'Status',
                    'options' => array(
                        'R' => 'Receipt',
                        'C' => 'Complete'
                    ),
                    'value' => ''
                ),
            ),
            'action_permissions' => array(
                'delete' => function ($model) {
                    return false;
                }
            ),
            'global_actions' => array(
                'clear_session' => array(
                    'title' => 'Release '.$transaction,
                    'action' => function($query)
                    {
                        Session::forget('orders_id');
                        return redirect('/admin/spkorea_qnas');
                    }
                )
            ),
            'form_width' => 450
        );
    }else {
        return array(
            'title' => 'Q&A',
            'single' => 'Q&A',
            'model' => App\Models\SpkoreaQnA::class,
            'columns' => array(
                'sq_id' => array(
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
                'sq_status' => array(
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
                'sq_question' => array(
                    'title' => 'Question'
                )
            ),
            'edit_fields' => array(
                'sq_id' => array(
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
                'sq_status' => array(
                    'title' => 'Status',
                    'type' => 'enum',
                    'options' => array(
                        'R' => 'Receipt',
                        'C' => 'Complete',
                    )
                ),
                'sq_question' => array(
                    'title' => 'Question',
                    'type' => 'textarea',
                    'height' => 130
                ),
                'sq_answer' => array(
                    'title' => 'Answer',
                    'type' => 'textarea',
                    'height' => 130
                )


            ),
            'actions' => array(),
            'filters' => array(
                'so_id' => array(
                    'title' => 'Order ID',
                    'type' => 'key',
                ),
                'sq_status' => array(
                    'type' => 'enum',
                    'title' => 'Status',
                    'options' => array(
                        'R' => 'Receipt',
                        'C' => 'Complete'
                    ),
                    'value' => ''
                ),
            ),
            'action_permissions' => array(
                'delete' => function ($model) {
                    return false;
                }
            ),
            'form_width' => 450
        );
    }
}
