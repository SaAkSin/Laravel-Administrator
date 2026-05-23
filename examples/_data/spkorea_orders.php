<?php
function formatOrderAmount($value)
{
    $safe_truncated = bcdiv($value, '1', 2);
    return number_format((float)$safe_truncated, 2);
}

function spkorea_orders()
{
    return array(
        'title' => 'Orders',
        'single' => 'Order',
        'model' => App\Models\SpkoreaOrder::class,
        'is_top_actions' => true,
        'columns' => array(
            'so_id' => array(
                'title' => 'ID'
            ),
            'location' => array(
                'title' => 'BOX Location',
                'output' => function($value, $model) {
                    $boxes = $model->forwardingBoxes()->select('sfb_location')->pluck('sfb_location');
                    return $boxes->implode(', ');
                }
            ),
            'user_name' => array(
                'title' => 'Name',
                'relationship' => 'user',
                'select' => '(:table).su_name',
                'output' => function ($value, $model) {
                    if ($model->so_status == 'S' && $value) {
                        $result = $model->user->orders()
                            ->where('so_status', 'S')
                            ->where('created_at', '<=', $model->created_at)
                            ->selectRaw('count(*) as count, sum(so_price) as total_price')
                            ->first();
                        $count = $result->count;
                        $sum = $result->total_price ?? 0; // 결과가 없을 경우 null 방지
                        return $value . '<br>' . $count . ' / $' . formatOrderAmount($sum);
                    }
                    return '';
                }
            ),
            'user_email' => array(
                'title' => 'E-mail',
                'relationship' => 'user',
                'select' => '(:table).email',
                'output' => function($value) {
                    if (!empty($value)) return  '<span onclick="copyLink(event, this)" style="text-decoration: underline">'.$value.'</span>';
                    else return '';
                }
            ),
            'guest_name' => array(
                'title' => 'Guest Name',
                'relationship' => 'guest',
                'select' => '(:table).sgt_name'
            ),
            'guest_email' => array(
                'title' => 'Guest E-mail',
                'relationship' => 'guest',
                'select' => '(:table).sgt_email',
                'output' => function($value) {
                    if (!empty($value)) return  '<span onclick="copyLink(event, this)" style="text-decoration: underline">'.$value.'</span>';
                    else return '';
                }
            ),
	        'number_item' => array(
	        	'title' => 'number of item',
		        'output' => function($value) {
	        		return '<center>'.$value.'</center>';
		        }
	        ),
	        'so_price' => array(
		        'title' => 'Amount',
		        'output' => function ($value) {
			        return '<div align="right">$' . formatOrderAmount($value) . '</div>';
		        }
	        ),
            'current_price_shipping' => array(
                'title' => 'Shipping Fee',
                'output' => function($value, $model) {
                    $price = (float)$model->so_price_shipping;
                    if (!is_null($price) && !is_null($value) && ($model->so_delivery_status == 'P' || $model->so_delivery_status == 'PD')) {
                        $currentPrice = (float)str_replace('$', '', $value);
                        if ($currentPrice == $price) {
                            return '$'.formatOrderAmount($price);
                        }  else {
                            return '<span style="color: red">$'.formatOrderAmount($price).'<br>($'.formatOrderAmount($currentPrice).')</span>';
                        }
                    }
                    return !is_null($price) ? '$'.$price : '';
                }
            ),
            'tax' => array(
                'title' => 'Tax Information',
                'output' => function($value, $model) {
                    $tax = $value ?? 0;
//                    $customsDuty = $model->so_customs_duty ?? 0;
//                    $clearanceFee = $model->so_clearance_fee ?? 0;
                    $postPaid = $model->so_duty_postpaid ?? false;
                    if ($tax) {
                        $tax = formatOrderAmount($tax);
                        if ($postPaid) {
                            return "<span style='color: red'>$0.00</br>(\${$tax})</span>";
                        } else {
                            return "\${$tax}";
                        }
                    }
                    return '';
                }
            ),
            'usedPoint' => array(
                'title' => 'Used credit'
            ),
	        'so_date' => array(
		        'title' => 'Date',
                'output' => function($value) {
                    $timezone = new DateTimeZone("Asia/Seoul");
                    $date = new DateTime($value);
                    $date->setTimezone($timezone);
                    return $date->format('Y-m-d H:i:s')." (Asia/Seoul)";
                }
	        ),
            'so_name' => array(
                'title' => 'Receiver',
            ),
            'so_type' => array(
                'title' => 'Payment Types',
                'output' => function ($value) {
                    switch ($value) {
                        case 'T':
                            return 'TEST';
                        case 'P':
                            return 'Paypal';
                        case 'Y':
                            return 'Yandex';
                        case 'S':
                            return 'Stripe';
                        default:
                            return 'N/A';
                    }
                }
            ),
            'so_status' => array(
                'title' => 'Status',
                'output' => function ($value) {
                    switch ($value) {
                        case 'P':
                            return 'Payment Ago';
                        case 'S':
                            return 'Complete Payment';
                        case 'C':
                            return 'Order Cancellation';
                        case 'R':
                            return 'Return';
                        case 'F':
                            return 'Payment Cancellation';
                        default:
                            return 'N/A';
                    }
                }
            ),
            'so_delivery_status' => array(
                'title' => 'Delivery Status',
                'output' => function ($value) {
                    switch ($value) {
                        case 'P':
                            return 'Preparing';
                        case 'PD':
                            return 'Shipping preparation';
                        case 'D':
                            return 'Delivering';
                        case 'C':
                            return 'Completed';
                        case 'N':
                            return 'None';
                        default:
                            return 'N/A';
                    }
                }
            ),
	        'so_mailling' => array(
		        'title' => 'Sending mail',
		        'output' => function($value) {
			        if($value) return '<center>O</center>';
			        else return '<center>X</center>';
		        }
	        ),
            'aiOrders' => array(
                'title' => 'AI',
                'relationship' => 'aiOrders',
                'select' => '(:table).ao_result',
                'output' => function($value) {
                    if (empty($value)) return 'N/A';
                    return $value == 'S' ? 'Success' : '<span style="color: red">Failed</span>';
                }
            ),
            'so_desc' => array(
                'title' => 'Order Information'
            )

        ),
        'edit_fields' => array(
            'so_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'forwardingBoxes' => array(
                'title' => 'SORT(Box Location)',
                'type' => 'relationship',
                'name_field' => 'sfb_location',
                'autocomplete' => true,
                'num_options' => 10,
                'options_filter' => function($query)
                {
                    $query->where('sfb_display', true)->whereNull('so_id'); //only returns living actors
                },
            ),
            'so_status' => array(
                'title' => 'Status',
                'type' => 'enum',
                'options' => array(
                    'P' => 'Payment Ago',
                    'S' => 'Complete Payment',
                    'C' => 'Order Cancellation',
                    'R' => 'Return',
                    'F' => 'Payment Cancellation'
                )
            ),
            'so_name' => array(
                'title' => 'Receiver',
                'limit' => 100
            ),
            'so_phone' => array(
                'title' => 'Phone',
                'limit' => 25
            ),
            'user' => array(
                'title' => 'Order of',
                'type' => 'relationship',
                'name_field' => 'search_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array("CONCAT(su_name, ' ', email)"),
                'editable' => function ($model) {
                    return false;
                }
            ),
            'guest' => array(
                'title' => 'Order of (Guest)',
                'type' => 'relationship',
                'name_field' => 'search_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array("CONCAT(sgt_name, ' ', sgt_email)"),
                'editable' => function ($model) {
                    return false;
                }
            ),
            'so_user' => array(
                'title' => 'User Type',
                'type' => 'enum',
                'options' => array(
                    'A' => 'Anonymous',
                    'S' => 'Member'
                )
            ),
            'so_company' => array(
                'title' => 'Company',
                'limit' => 100
            ),
            'so_tax' => array(
                'title' => 'Tax ID, VAT#, ETC',
                'limit' => 50
            ),
            'so_delivery_status' => array(
                'title' => 'Delivery Status',
                'type' => 'enum',
                'options' => array(
                    'P' => 'Preparing goods',
                    'PD' => 'Shipping preparation',
                    'D' => 'Delivering',
                    'C' => 'Complete',
                    'N' => 'None'
                ),
                'visible' => function ($model) {
                    // 결제 완료인 경우에만 배송상태 변경 가능하도록 수정
                    // editable로 처리한 경우 visible/invisible 처리는 되나 저장이 되자 않는 현상 발생
                    return $model->so_status === 'S';
                }
//                'editable' => function ($model) {
//                    // 결제 완료인 경우에만 배송상태 변경 가능하도록 수정
////                    Log::info($model->so_status);
//                    if ($model->exists && $model->so_status === 'S') {
//                        return true;
//                    }
//
//                    return false;
//                }
            ),
            'so_delivery_code' => array(
                'title' => 'Invoice Number',
                'limit' => 50
            ),
            'so_num' => array(
                'title' => 'Order Number',
                'limit' => 20
            ),
            'so_type' => array(
                'title' => 'Payment Types',
                'type' => 'enum',
                'options' => array(
                    'T' => 'TEST',
                    'P' => 'Paypal',
                    'Y' => 'Yandex',
                    'S' => 'Stripe'
                ),
                'editable' => function($model)
                {
                    return false;
                }
            ),

            'so_code' => array(
                'title' => 'PG Code',
                'type' => 'text',
                'limit' => 100,
                'editable' => function($model)
                {
                    return is_null($model->so_code);
                }
            ),
            'so_date' => array(
                'title' => 'Date',
                'type' => 'datetime'
            ),
            'so_price' => array(
                'title' => 'Amount of Payment',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
            'so_price_shipping' => array(
                'title' => 'Shipping Fee',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
            'so_price_goods' => array(
                'title' => 'Deals',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
            'so_customs_duty' => array(
                'title' => 'Customs Duty',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
            'so_clearance_fee' => array(
                'title' => 'Clearance fee',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
            'so_duty_postpaid' => array(
                'title' => 'Duty Postpaid',
                'type' => 'bool',
                'editable' => false
            ),
            'so_weight' => array(
                'title' => 'Weight',
                'type' => 'number',
                'symbol' => 'kg',
                'decimals' =>2
            ),
            'so_ship' => array(
                'title' => 'Ship',
                'type' => 'enum',
                'options' => array(
                    'ap' => 'AIR PARCEL',
                    'dhl' => 'DHL',
                    'ems' => 'EMS',
                    'sp' => 'SMALL PACKET',
                    'ups' => 'UPS',
                    'fedex' => 'FEDEX',
                    'op' => 'OCEAN POST',
                )
            ),
            'so_zip' => array(
                'title' => 'ZIP',
                'limit' => 10
            ),
            'so_addr1' => array(
                'title' => 'Address #1',
                'limit' => 255
            ),
            'so_addr2' => array(
                'title' => 'Address #2',
                'limit' => 255
            ),
            'so_city' => array(
                'title' => 'City',
                'limit' => 100
            ),
            'so_state' => array(
                'title' => 'State',
                'limit' => 100
            ),
            'country' => array(
                'title' => 'Country',
                'type' => 'relationship',
                'name_field' => 'sc_country',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('sc_country'),
            ),
            'so_country' => array(
                'title' => 'Country(Other)',
                'limit' => 100
            ),
            'so_desc' => array(
                'title' => 'Message',
                'type' => 'textarea',
                'height' => 130,
            ),
            'aiOrders' => array(
                'title' => 'Memo(Translation)',
                'type' => 'relationship',
                'name_field' => 'ao_memo',
                'editable' => false,
            ),
            'so_memo' => array(
                'title' => 'Memo',
                'type' => 'textarea',
                'height' => 130,
            )

        ),
        'actions' => array(
            'orders_detail' => array(
                'title' => 'orders detail',
                'action' => function ($model) {
                    Session::put('orders_id', $model->so_id);
                    return redirect('/admin/spkorea_orders_detail');
                }
            ),
            'orders_qna' => array(
                'title' => 'Q&A',
                'action' => function($model) {
                    Session::put('orders_id', $model->so_id);
                    return redirect('/admin/spkorea_qnas');
                }
            ),
            'download_invoice' => array(
                'title' => 'Download invoice',
                'permission' => function($model) {
//                    return ($model->so_delivery_status === 'C');
                    return true;
                },
                'action' => function($model) {
                    return redirect('/invoiceorder/'.$model->so_id);
                }
            ),
            'download_dhl_format' => array(
                'title' => 'Download DHL format',
                'permission' => function($model) {
//                    return ($model->so_delivery_status === 'C');
                    return true;
                },
                'action' => function($model) {
                    return redirect('/invoiceorder/dhl/'.$model->so_id);
                }
            ),
            'points_detail' => array(
                'title' => 'credits detail',
                'action' => function ($model) {
                    Session::put('orders_id', $model->so_id);
                    return redirect('/admin/spkorea_points');
                }
            ),
	        'send_mail' => array(
	        	'title' => 'send a shipping mail',
		        'message' => array(
		        	'active' => 'Sending...',
			        'success' => 'Completed',
			        'error' => 'Failed'
		        ),
		        'permission' => function($model) {
                    // return !$model->so_mailling && ($model->so_delivery_status === 'D') && ($model->so_delivery_code);
                    return ($model->so_delivery_status === 'D') && ($model->so_delivery_code);
		        },
		        'action' => function($model) {
	        		if ($model->sendMail()) {
	        			$model->so_mailling = true;
	        			return $model->save();
			        }
	        		return false;
		        }
	        ),
            'send_order_mail' => array(
                'title' => 'send an order mail',
                'message' => array(
                    'active' => 'Sending...',
                    'success' => 'Completed',
                    'error' => 'Failed'
                ),
                'action' => function($model) {
                    return $model->sendOrderMail();
                }
            )
        ),
        'filters' => array(
            'so_id' => array(
                'title' => 'Order ID',
                'type' => 'key'
            ),
            'so_num' => array(
                'title' => 'Order Number',
                'type' => 'text'
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
            'guest' => array(
                'title' => 'User (Guest)',
                'type' => 'relationship',
                'description' => 'Name/E-mail.',
                'name_field' => 'search_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('CONCAT(sgt_name, sgt_email)'),
            ),
            'so_delivery_code' => array(
                'title' => 'Invoice Number',
                'type' => 'text'
            ),
            'so_code' => array(
                'title' => 'PG Code',
                'type' => 'text'
            ),
        ),
        'action_permissions' => array(
            'delete' => function ($model) {
                return false;
            }
        ),
        'form_width' => 450,
        'link' => function($model)
        {
            return '/print-order/'.$model->so_id;
        }
    );
}
