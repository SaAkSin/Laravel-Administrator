<?php
function spkorea_goods_options()
{
    $sgg_id = Session::get('group_id');
    if($sgg_id) {
        $group = \App\Models\SpkoreaGoodsGroup::find($sgg_id);
        $sgg_group = $group->sgg_group;
        if(strlen($sgg_group) > 10) {
            $sgg_group = substr($sgg_group, 0, 10).'...';
        }

        $goods = $group->goods()->first();
        $sg_name = $goods->sg_name;
        if(strlen($sg_name) > 10) {
            $sg_name = substr($sg_name, 0, 10).'...';
        }

        return array(
            'title' => 'Options ('.$sg_name.' - '.$sgg_group.')',
            'single' => 'Option',
            'model' => App\Models\SpkoreaGoodsOption::class,
            'columns' => array(
                'sgo_id' => array(
                    'title' => 'ID'
                ),
                'goods' => array(
                    'title' => 'Goods',
                    'relationship' => 'group.goods',
                    'select' => '(:table).sg_name'
                ),
                'group' => array(
                    'title' => 'Group',
                    'relationship' => 'group',
                    'select' => 'CONCAT((:table).sgg_id, " -  ", (:table).sgg_group)'
                ),
                'sgo_name' => array(
                    'title' => 'Name'
                ),
                'sgo_price' => array(
                    'title' => 'Price',
                    'output' => function($value) {
                        return number_format($value);
                    }
                ),
                'sgo_retail' => array(
                    'title' => 'Price(USD)'
                ),
                'sgo_weight' => array(
                    'title' => 'Weight'
                ),
                'sgo_status' => array(
                    'title' => 'Status',
                    'output' => function ($value) {
                        switch ($value) {
                            case 'S1':
                                return 'Sale';
                            case 'O1':
                                return 'Sold Out';
                            default:
                                return 'N/A';
                        }
                    }
                )

            ),
            'edit_fields' => array(
                'sgo_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                ),
                'group' => array(
                    'title' => 'Group',
                    'type' => 'relationship',
                    'value' => $sgg_id,
                    'name_field' => 'sgg_group',
                    'editable' => function ($model) {
                        return !$model->exists;
                    }
                ),
                'sgo_name' => array(
                    'title' => 'Name',
                    'type' => 'text',
                    'limit' => 20
                ),
                'sgo_price' => array(
                    'title' => 'Price',
                    'type' => 'number'
                ),
                'sgo_retail' => array(
                    'title' => 'Price(USD)',
                    'type' => 'number',
                    'symbol' => '$',
                    'decimals' => 2
                ),
                'sgo_weight' => array(
                    'title' => 'Weight',
                    'type' => 'number',
                    'symbol' => 'kg',
                    'decimals' => 2
                ),
                'sgo_status' => array(
                    'title' => 'Status',
                    'type' => 'enum',
                    'options' => array(
                        'S1' => 'on sale',
                        'O1' => 'sold out'
                    )
                ),
                'sgo_desc' => array(
                    'title' => 'Memo',
                    'type' => 'textarea'
                )
            ),
            'actions' => array(
                'update_price' => array(
                    'title' => 'update price for option',
                    'confirmation' => 'Are you sure you want to update?',
                    'action' => function ($model) {
                        $exchange = DB::table('spkorea_exchanges')->orderBy('created_at', 'desc')->first();
                        if (!is_null($exchange)) {
                            if ($exchange->sx_usd > 0) {
                                $model->sgo_retail = $model->sgo_price / $exchange->sx_usd;
                                return $model->save();
                            }
                        }
                        return false;
                    }
                ),
            ),
            'filters' => array(
                'sgg_id' => array(
                    'title' => 'Group ID',
                    'type' => 'key',
                    'value' => $sgg_id
                ),
                'sgo_name' => array(
                    'title' => 'Name',
                    'type' => 'text'
                ),
            ),
            'global_actions' => array(
                'clear_session' => array(
                    'title' => 'Release '.$sg_name.' - '.$sgg_group,
                    'action' => function($query)
                    {
                        Session::forget('group_id');
                        return redirect('/admin/spkorea_goods_options');
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
            'title' => 'Options',
            'single' => 'Option',
            'model' => App\Models\SpkoreaGoodsOption::class,
            'columns' => array(
                'sgo_id' => array(
                    'title' => 'ID'
                ),
                'goods' => array(
                    'title' => 'Goods',
                    'relationship' => 'group.goods',
                    'select' => '(:table).sg_name'
                ),
                'group' => array(
                    'title' => 'Group',
                    'relationship' => 'group',
                    'select' => 'CONCAT((:table).sgg_id, " -  ", (:table).sgg_group)'
                ),
                'sgo_name' => array(
                    'title' => 'Name'
                ),
                'sgo_price' => array(
                    'title' => 'Price',
                    'output' => function($value) {
                        return number_format($value);
                    }
                ),
                'sgo_retail' => array(
                    'title' => 'Price(USD)'
                ),
                'sgo_weight' => array(
                    'title' => 'Weight'
                ),
                'sgo_status' => array(
                    'title' => 'Status',
                    'output' => function ($value) {
                        switch ($value) {
                            case 'S1':
                                return 'Sale';
                            case 'O1':
                                return 'Sold Out';
                            default:
                                return 'N/A';
                        }
                    }
                )

            ),
            'edit_fields' => array(
                'sgo_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                ),
                'group' => array(
                    'title' => 'Group',
                    'type' => 'relationship',
                    'name_field' => 'sgg_group',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array('CONCAT(sgg_id, " - ", sgg_group)'),
                ),
                'sgo_name' => array(
                    'title' => 'Name',
                    'type' => 'text',
                    'limit' => 20
                ),
                'sgo_price' => array(
                    'title' => 'Price',
                    'type' => 'number'
                ),
                'sgo_retail' => array(
                    'title' => 'Price(USD)',
                    'type' => 'number',
                    'symbol' => '$',
                    'decimals' => 2
                ),
                'sgo_weight' => array(
                    'title' => 'Weight',
                    'type' => 'number',
                    'symbol' => 'kg',
                    'decimals' => 2
                ),
                'sgo_status' => array(
                    'title' => 'Status',
                    'type' => 'enum',
                    'options' => array(
                        'S1' => 'on sale',
                        'O1' => 'sold out'
                    )
                ),
                'sgo_desc' => array(
                    'title' => 'Memo',
                    'type' => 'textarea'
                )
            ),
            'actions' => array(
                'update_price' => array(
                    'title' => 'update price for option',
                    'confirmation' => 'Are you sure you want to update?',
                    'action' => function ($model) {
                        $exchange = DB::table('spkorea_exchanges')->orderBy('created_at', 'desc')->first();
                        if (!is_null($exchange)) {
                            if ($exchange->sx_usd > 0) {
                                $model->sgo_retail = $model->sgo_price / $exchange->sx_usd;
                                return $model->save();
                            }
                        }
                        return false;
                    }
                ),
            ),
            'filters' => array(
                'sgg_id' => array(
                    'title' => 'Group ID',
                    'type' => 'key'
                ),
                'sgo_name' => array(
                    'title' => 'Name',
                    'type' => 'text'
                )
            ),
//            'permission'=> function()
//            {
//                $user = auth()->user();
//                if ($user) {
//                    return !$user->isSubAdmin();
//                }
//                return false;
//            },
            'form_width' => 450
        );
    }
}
