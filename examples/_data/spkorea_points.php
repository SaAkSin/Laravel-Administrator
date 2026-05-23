<?php
function spkorea_points()
{
    $so_id = Session::get('orders_id');

    if ($so_id) {
        $order = App\Models\SpkoreaOrder::find($so_id);

        return array(
            'title' => 'Member\'s Credit - Order('.$order->so_id.')',
            'single' => 'Credit',
            'model' => App\Models\SpkoreaPoint::class,
            'columns' => array(
                'spp_id' => array(
                    'title' => 'ID'
                ),
                'user' => array(
                    'title' => 'Credit of',
                    'relationship' => 'user',
                    'select' => 'CONCAT((:table).su_name, " -  ", (:table).email)'
                ),
                'spp_email' => array(
                    'title' => 'Send Mail',
                    'output' => function ($value) {
                        if ($value) return 'O';
                        return 'X';
                    }
                ),
                'credit_total' => array(
                    'title' => 'Total Credits',
                    'output' => function($value) {
                        if ($value >= 0) {
                            return '<span style="color: red;">'.number_format($value, 2).' USD</span>';
                        } else {
                            return '<span style="color: blue;">'.number_format($value, 2).' USD</span>';
                        }
                    }
                ),
                'spp_type' => array(
                    'title' => 'Type',
                    'output' => function($value) {
                        switch ($value) {
                            case 'I':
                                return 'Inc';
                            case 'D':
                                return 'Dec';
                            case 'R':
                                return 'Ret';
                            default:
                                return 'N/A';
                        }
                    }
                ),
                'spp_point' => array(
                    'title' => 'Credit',
                    'output' => function($value) {
                        if ($value >= 0) {
                            return '<span style="color: red;">'.number_format($value, 2).' USD</span>';
                        } else {
                            return '<span style="color: blue;">'.number_format($value, 2).' USD</span>';
                        }
                    }
                ),
                'spp_key' => array(
                    'title' => 'Order ID'
                )

            ),
            'edit_fields' => array(
                'spp_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                ),
                'user' => array(
                    'title' => 'Credit of',
                    'type' => 'relationship',
                    'name_field' => 'search_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array("CONCAT(su_name, ' ', email)"),
                    'editable' => function ($model) {
                        return !$model->exists;
                    }
                ),
                'spp_key' => array(
                    'title' => 'Order ID',
                    'type' => 'text',
                    'editable' => function ($model) {
                        return false;
                    }
                ),
                'spp_point' => array(
                    'title' => 'Credit',
                    'type' => 'number',
                    'symbol' => '$',
                    'decimals' => 2
                ),
                'spp_type' => array(
                    'title' => 'Type',
                    'type' => 'enum',
                    'options' => array(
                        'I' => 'Increase',
                        'D' => 'Decrease',
                        'R' => 'Return'
                    )
                ),
                'spp_desc' => array(
                    'title' => 'Message',
                    'type' => 'textarea',
                    'height' => 130,
                ),
                'spp_memo' => array(
                    'title' => 'Memo',
                    'type' => 'textarea',
                    'height' => 130,
                ),
            ),
            'actions' => array(
                'send_mail' => array(
                    'title' => 'send mail',
                    'message' => array(
                        'active' => 'Sending...',
                        'success' => 'Completed',
                        'error' => 'Failed'
                    ),
                    'permission' => function($model) {
                        return !$model->spp_email && ($model->spp_type == 'I' || $model->spp_type == 'R');
                    },
                    'action' => function($model) {
                        if ($model->sendMail()) {
                            $model->spp_email = true;
                            return $model->save();
                        }
                        return false;
                    }
                )
            ),
            'filters' => array(
                'spp_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                ),
                'user' => array(
                    'title' => 'User',
                    'type' => 'relationship',
                    'description' => 'Name/E-mail.',
                    'name_field' => 'search_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array('CONCAT(su_name, email)'),
                ),
                'spp_key' => array(
                    'title' => 'Order ID',
                    'type' => 'text',
                    'value' => $so_id
                ),

            ),
            'global_actions' => array(
                'clear_session' => array(
                    'title' => 'Release '.$order->so_id,
                    'action' => function($query)
                    {
                        Session::forget('orders_id');
                        return redirect('/admin/spkorea_points');
                    }
                )
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

    } else {
        return array(
            'title' => 'Member\'s Credit',
            'single' => 'Credit',
            'model' => App\Models\SpkoreaPoint::class,
            'columns' => array(
                'spp_id' => array(
                    'title' => 'ID'
                ),
                'user' => array(
                    'title' => 'Credit of',
                    'relationship' => 'user',
                    'select' => 'CONCAT((:table).su_name, " -  ", (:table).email)'
                ),
                'spp_email' => array(
                    'title' => 'Send Mail',
                    'output' => function ($value) {
                        if ($value) return 'O';
                        return 'X';
                    }
                ),
                'credit_total' => array(
                    'title' => 'Total Credits',
                    'output' => function($value) {
                        if ($value >= 0) {
                            return '<span style="color: red;">'.number_format($value, 2).' USD</span>';
                        } else {
                            return '<span style="color: blue;">'.number_format($value, 2).' USD</span>';
                        }
                    }
                ),
                'spp_type' => array(
                    'title' => 'Type',
                    'output' => function($value) {
                        switch ($value) {
                            case 'I':
                                return 'Inc';
                            case 'D':
                                return 'Dec';
                            case 'R':
                                return 'Ret';
                            default:
                                return 'N/A';
                        }
                    }
                ),
                'spp_point' => array(
                    'title' => 'Credit',
                    'output' => function($value) {
                        if ($value >= 0) {
                            return '<span style="color: red;">'.number_format($value, 2).' USD</span>';
                        } else {
                            return '<span style="color: blue;">'.number_format($value, 2).' USD</span>';
                        }
                    }
                ),
                'spp_key' => array(
                    'title' => 'Order ID'
                )

            ),
            'edit_fields' => array(
                'spp_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                ),
                'user' => array(
                    'title' => 'Credit of',
                    'type' => 'relationship',
                    'name_field' => 'search_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array("CONCAT(su_name, ' ', email)"),
                    'editable' => function ($model) {
                        return !$model->exists;
                    }
                ),
                'spp_key' => array(
                    'title' => 'Order ID',
                    'type' => 'text',
                    'editable' => function ($model) {
                        return false;
                    }
                ),
                'spp_point' => array(
                    'title' => 'Credit',
                    'type' => 'number',
                    'symbol' => '$',
                    'decimals' => 2
                ),
                'spp_type' => array(
                    'title' => 'Type',
                    'type' => 'enum',
                    'options' => array(
                        'I' => 'Increase',
                        'D' => 'Decrease',
                        'R' => 'Return'
                    )
                ),
                'spp_desc' => array(
                    'title' => 'Message',
                    'type' => 'textarea',
                    'height' => 130,
                ),
                'spp_memo' => array(
                    'title' => 'Memo',
                    'type' => 'textarea',
                    'height' => 130,
                ),
            ),
            'actions' => array(
                'send_mail' => array(
                    'title' => 'send mail',
                    'message' => array(
                        'active' => 'Sending...',
                        'success' => 'Completed',
                        'error' => 'Failed'
                    ),
                    'permission' => function($model) {
                        return !$model->spp_email && ($model->spp_type == 'I' || $model->spp_type == 'R');
                    },
                    'action' => function($model) {
                        if ($model->sendMail()) {
                            $model->spp_email = true;
                            return $model->save();
                        }
                        return false;
                    }
                )
            ),
            'filters' => array(
                'spp_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                ),
                'user' => array(
                    'title' => 'User',
                    'type' => 'relationship',
                    'description' => 'Name/E-mail.',
                    'name_field' => 'search_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array('CONCAT(su_name, email)'),
                ),
                'spp_key' => array(
                    'title' => 'Order ID',
                    'type' => 'text'
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
}
