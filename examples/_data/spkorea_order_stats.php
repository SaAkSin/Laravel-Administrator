<?php
function spkorea_order_stats()
{
    return array(
        'title' => 'Order Stats',
        'single' => 'OrderStat',
        'model' => App\Models\SpkoreaOrderStat::class,
        'is_top_actions' => true,
        'columns' => array(
            'user' => array(
                'title' => 'Order of',
                'relationship' => 'user',
                'select' => 'CONCAT((:table).su_name, " -  ", (:table).email)'
            ),
            'sos_count' => array(
                'title' => 'Order Count',
                'output' => function ($value) {
                    return '<div align="right">' . $value . '</div>';
                }
            ),
            'sos_sum_price' => array(
                'title' => 'Sum of Price',
                'output' => function ($value) {
                    return '<div align="right">$' . floor($value*100)/100 . '</div>';
                }
            ),
            'so_price_goods' => array(
                'title' => 'Sum of Goods',
                'output' => function ($value) {
                    return '<div align="right">$' . floor($value*100)/100 . '</div>';
                }
            ),
            'so_price_shipping' => array(
                'title' => 'Sum of Shipping',
                'output' => function ($value) {
                    return '<div align="right">$' . floor($value*100)/100 . '</div>';
                }
            ),
            'sos_order_date' => array(
                'title' => 'Order Date'
            )
        ),
        'edit_fields' => array(
            'sos_id' => array(
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
            'sos_count' => array(
                'title' => 'Order Count',
                'editable' => function ($model) {
                    return false;
                }
            ),
            'sos_sum_price' => array(
                'title' => 'Sum of Price',
                'editable' => function ($model) {
                    return false;
                }
            ),
            'so_price_goods' => array(
                'title' => 'Sum of Goods',
                'editable' => function ($model) {
                    return false;
                }
            ),
            'so_price_shipping' => array(
                'title' => 'Sum of Shipping',
                'editable' => function ($model) {
                    return false;
                }
            ),
            'sos_order_date' => array(
                'title' => 'Order Date',
                'editable' => function ($model) {
                    return false;
                }
            )
        ),
        'actions' => array(
            'go_to_user' => array(
                'title' => 'Go to user',
                'action' => function ($model) {

                }
            ),
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
            'sos_order_date' => array(
                'title' => 'Order Date (YYYY-MM)',
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
