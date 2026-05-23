<?php
function spkorea_pay_stripe_logs()
{
    return array(
        'title' => 'Pay Stripe Logs',
        'single' => 'PayStripeLog',
        'model' => App\Models\SpkoreaPayStripeLog::class,
        'is_top_actions' => true,
        'columns' => array(
            'stl_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'user' => array(
                'title' => 'Order of',
                'relationship' => 'user',
                'select' => 'CONCAT((:table).su_name, " -  ", (:table).email)'
            ),
            'guest' => array(
                'title' => 'Order of (Guest)',
                'relationship' => 'guest',
                'select' => 'CONCAT((:table).sgt_name, " -  ", (:table).sgt_email)'
            ),
            'spo_id' => array(
                'title' => 'PreOrder ID',
            ),
            'stl_type' => array(
                'title' => 'Type',
                'output' => function ($value) {
                    switch ($value) {
                        case 'D':
                            return 'Debug';
                        case 'E':
                            return 'Error';
                        case 'S':
                            return 'Success';
                        default:
                            return $value;
                    }
                }
            ),
            'stl_subject' => array(
                'title' => 'Subject',
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
        ),
        'edit_fields' => array(
            'stl_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'user' => array(
                'title' => 'User',
                'type' => 'relationship',
                'description' => 'Name',
                'name_field' => 'su_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('CONCAT(su_name, " - ", email)'),
                'editable' => function ($model) {
                    return false;
                }
            ),
            'guest' => array(
                'title' => 'User (Guest)',
                'type' => 'relationship',
                'name_field' => 'search_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array("CONCAT(sgt_name, ' ', sgt_email)"),
                'editable' => function ($model) {
                    return false;
                }
            ),
            'spo_id' => array(
                'title' => 'PreOrder ID',
                'type' => 'text',
                'editable' => function ($model) {
                    return false;
                }
            ),
            'stl_session' => array(
                'title' => 'session_id',
                'type' => 'text',
            ),
            'stl_type' => array(
                'title' => 'Type',
                'type' => 'enum',
                'options' => array(
                    'D' => 'Debug',
                    'E' => 'Error',
                    'S' => 'Success',
                ),
                'editable' => function ($model) {
                    return false;
                }
            ),
            'stl_subject' => array(
                'title' => 'Subject',
                'type' => 'text',
            ),
            'stl_raw' => array(
                'title' => 'Raw',
                'type' => 'textarea',
            ),
            'stl_comment' => array(
                'title' => 'Comment',
                'type' => 'textarea',
            ),
        ),
        'actions' => array(
//            'go_to_user' => array(
//                'title' => 'Go to user',
//                'action' => function ($model) {
//
//                }
//            ),
        ),
        'filters' => array(
            'user' => array(
                'title' => 'User',
                'type' => 'relationship',
                'description' => 'Name/E-mail.',
                'name_field' => 'search_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('CONCAT(su_name, email)'),
            ),
            'guest' => array(
                'title' => 'User (Guest)',
                'type' => 'relationship',
                'description' => 'Name/E-mail.',
                'name_field' => 'search_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('CONCAT(sgt_name, sgt_email)'),
            ),
            'spo_id' => array(
                'title' => 'PreOrder ID',
                'type' => 'text'
            )
        ),
        'action_permissions' => array(
            'create' => function ($model) {
                return false;
            },
            'update' => function ($model) {
                return false;
            },
            'delete' => function ($model) {
                return false;
            },
        ),
        'form_width' => 450,
//        'link' => function($model)
//        {
//            return '/admin/spkorea_users/'.$model->su_id;
//        }
    );
}
