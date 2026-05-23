<?php
function spkorea_orders_detail()
{
    $so_id = Session::get('orders_id');

    if ($so_id) {
        $order = App\Models\SpkoreaOrder::find($so_id);
        $transaction =  strval($so_id).' - '.$order->so_code;

        return array(
            'title' => 'Orders Detail ('.$transaction.')',
            'single' => 'Item',
            'model' => App\Models\SpkoreaCart::class,
            'columns' => array(
                'sr_id' => array(
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
                'su_id' => array(
                    'title' => 'User',
                    'relationship' => 'user',
                    'select' => 'CONCAT((:table).su_name, " (", (:table).email,")")'
                ),
                'guest' => array(
                    'title' => 'User (Guest)',
                    'relationship' => 'guest',
                    'select' => 'CONCAT((:table).sgt_name, " -  ", (:table).sgt_email)'
                ),
                'sr_type' => array(
                    'title' => 'Type',
                    'output' => function ($value) {
                        switch ($value) {
                            case 'A':
                                return 'Goods';
                            case 'P':
                                return 'Parts';
                            default:
                                return '';
                        }
                    }
                ),
//                'sg_id' => array(
//                    'title' => 'Goods ID',
//                    'output' => function($value) {
//                        return '<a href="/admin/spkorea_goods/'.$value.'">'.$value.'</a>';
//                    }
//                ),
//                'sp_id' => array(
//                    'title' => 'Parts ID',
//                    'output' => function($value) {
//                        return '<a href="/admin/spkorea_parts/'.$value.'">'.$value.'</a>';
//                    }
//                ),
                'sr_name' => array(
                    'title' => 'Name'
                ),
                'part' => array(
                	'title' => 'Part Number',
	                'relationship' => 'part',
	                'select' => '(:table).sp_no'
                ),
                'sr_number' => array(
                    'title' => 'Number'
                ),
                'sr_weight' => array(
                    'title' => 'Weight'
                ),
                'sr_money' => array(
                    'title' => 'Price(USD)'
                ),
                'sr_option' => array(
                    'title' => 'Option',
                    'output' => function($value)
                    {
                        $options = json_decode($value);
                        $temp = '';
                        if(is_array($options)) {
                            foreach($options as $idx => $option) {
                                if($idx === 0) {
                                    $temp = $option->sgo_name;
                                }else {
                                    $temp = $temp.', '.$option->sgo_name;
                                }
                            }
                        }
                        return $temp;
                    }
                )
            ),
            'edit_fields' => array(
                'sr_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                )
            ),
	        'actions' => array(
		        'input_weight' => array(
			        'title' => 'input weight',
			        'permission' => function($model)
			        {
				        return ($model->sr_type == 'P') && ($model->sr_weight == 0.0);
			        },
			        'action' => function($model)
			        {
				        Session::put('part_id', $model->sp_id);
				        return redirect('/admin/spkorea_parts/'.$model->sp_id);
			        }
		        )
	        ),
            'filters' => array(
                'so_id' => array(
                    'title' => 'Order ID',
                    'type' => 'key',
                    'value' => $so_id
                ),
                'sr_status' => array(
                    'type' => 'enum',
                    'title' => 'Status',
                    'options' => array(
                        'O' => 'Order',
                        'N' => 'Cart'
                    ),
                    'value' => 'O'
                ),
                'user' => array(
                    'title' => 'Name',
                    'type' => 'relationship',
                    'name_field' => 'su_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array("CONCAT(su_name, ' ', email)")
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
            ),
            'global_actions' => array(
                'clear_session' => array(
                    'title' => 'Release '.$transaction,
                    'action' => function($query)
                    {
                        Session::forget('orders_id');
                        return redirect('/admin/spkorea_orders_detail');
                    }
                ),
                'download_csv' => array(
                    'title' => 'Download CSV',
                    'action' => function($query)
                    {
                        $so_id = Session::get('orders_id');
                        if ($so_id) {
                            return redirect('/export_order_details/' . $so_id);
                        }

                        return false;
                    }
                ),
            ),
            'form_width' => 450,
            'link' => function($model) {
                if($model->sr_type == 'A') {
                    $goods = $model->goods()->first();
                    if($goods) {
                        $category = $goods->category()->first();
                        if(!is_null($category)) {
                            return env('WEB_URL').'/category/'.$category->sy_id.'/'.$category->sy_main.'/'.$model->sg_id;
                        }
                    }
                }else if($model->sr_type == 'P') {
                    $part = $model->part()->first();
                    if($part) {
                        return env('WEB_URL').'/product/parts/'.$part->sp_id.'?no='.$part->sp_no;
                    }
                }
                return '';
            }
        );
    }else {
        return array(
            'title' => 'Orders Detail',
            'single' => 'Item',
            'model' => App\Models\SpkoreaCart::class,
            'columns' => array(
                'sr_id' => array(
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
                'su_id' => array(
                    'title' => 'User',
                    'relationship' => 'user',
                    'select' => 'CONCAT((:table).su_name, " (", (:table).email,")")'
                ),
                'guest' => array(
                    'title' => 'User (Guest)',
                    'relationship' => 'guest',
                    'select' => 'CONCAT((:table).sgt_name, " -  ", (:table).sgt_email)'
                ),
                'sr_type' => array(
                    'title' => 'Type',
                    'output' => function ($value) {
                        switch ($value) {
                            case 'A':
                                return 'Goods';
                            case 'P':
                                return 'Parts';
                            default:
                                return '';
                        }
                    }
                ),
//                'sg_id' => array(
//                    'title' => 'Goods ID',
//                    'output' => function($value) {
//                        return $value;
//                    }
//                ),
//                'sp_id' => array(
//                    'title' => 'Parts ID',
//                    'output' => function($value) {
//                        return $value;
//                    }
//                ),
                'sr_name' => array(
                    'title' => 'Name'
                ),
	            'part' => array(
		            'title' => 'Part Number',
		            'relationship' => 'part',
		            'select' => '(:table).sp_no'
	            ),
                'sr_number' => array(
                    'title' => 'Number'
                ),
                'sr_weight' => array(
                    'title' => 'Weight'
                ),
                'sr_money' => array(
                    'title' => 'Price(USD)'
                ),
                'sr_option' => array(
                    'title' => 'Option',
                    'output' => function($value)
                    {
                        $options = json_decode($value);
                        $temp = '';
                        if(is_array($options)) {
                            foreach($options as $idx => $option) {
                                if($idx === 0) {
                                    $temp = $option->sgo_name;
                                }else {
                                    $temp = $temp.', '.$option->sgo_name;
                                }
                            }
                        }
                        return $temp;
                    }
                )
            ),
            'edit_fields' => array(
                'sr_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                )
            ),
            'actions' => array(
            	'input_weight' => array(
            		'title' => 'input weight',
		            'permission' => function($model)
		            {
		            	return ($model->sr_type == 'P') && ($model->sr_weight == 0.0);
		            },
		            'action' => function($model)
		            {
		            	Session::put('part_id', $model->sp_id);
			            return redirect('/admin/spkorea_parts/'.$model->sp_id);
		            }
	            )
            ),
            'filters' => array(
                'so_id' => array(
                    'title' => 'Order ID',
                    'type' => 'key'
                ),
                'sr_status' => array(
                    'type' => 'enum',
                    'title' => 'Status',
                    'options' => array(
                        'O' => 'Order',
                        'N' => 'Cart'
                    ),
	                'value' => 'O'
                ),
                'user' => array(
                    'title' => 'Name',
                    'type' => 'relationship',
                    'name_field' => 'su_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array("CONCAT(su_name, ' ', email)")
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
            ),
            'form_width' => 450,
            'link' => function($model) {
                if($model->sr_type == 'A') {
                    $goods = $model->goods()->first();
                    if($goods) {
                        $category = $model->category()->first();
                        if(!is_null($category)) {
                            return env('WEB_URL').'/category/'.$category->sy_id.'/'.$category->sy_main.'/'.$model->sg_id;
                        }else {
                            return '';
                        }
                    }
                    return '';
                }else if($model->sr_type == 'P') {
	                $part = $model->part()->first();
	                if($part) {
		                return env('WEB_URL').'/product/parts/'.$part->sp_id.'?no='.$part->sp_no;
	                }
                }
                return '';
            }
        );
    }
}
