<?php
function spkorea_goods_groups()
{
    $sg_id = Session::get('goods_id');

    if($sg_id) {
        $goods = \App\Models\SpkoreaGoods::find($sg_id);
        $sg_name = $goods->sg_name;
        if(strlen($sg_name) > 10) {
            $sg_name = substr($sg_name, 0, 10).'...';
        }

        return array(
            'title' => 'Option Groups ('.$sg_name.')',
            'single' => 'Group',
            'model' => App\Models\SpkoreaGoodsGroup::class,
            'columns' => array(
                'sgg_id' => array(
                    'title' => 'ID'
                ),
                'sg_id' => array(
                    'title' => 'Goods',
                    'relationship' => 'goods',
                    'select' => '(:table).sg_name'
                ),
                'sgg_group' => array(
                    'title' => 'Name'
                ),
                'sgg_required' => array(
                    'title' => 'Essential',
                    'output' => function ($value) {
                        if ($value) {
                            return 'TRUE';
                        }
                        return 'FALSE';
                    }
                )

            ),
            'edit_fields' => array(
                'sgg_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                ),
                'goods' => array(
                    'title' => 'Goods',
                    'type' => 'relationship',
                    'value' => $sg_id,
                    'name_field' => 'sg_name',
                    'editable' => function ($model) {
                        return !$model->exists;
                    }
                ),
                'sgg_group' => array(
                    'title' => 'Name',
                    'type' => 'text',
                    'limit' => 20
                ),
                'sgg_required' => array(
                    'title' => 'Essential',
                    'type' => 'bool'
                )

            ),
            'actions' => array(
                'add_option' => array(
                    'title' => 'add options',
                    'action' => function ($model) {
                        Session::put('group_id', $model->sgg_id);
                        return redirect('/admin/spkorea_goods_options/new');
                    }
                ),
            ),
            'filters' => array(
                'sg_id' => array(
                    'title' => 'Goods ID',
                    'type' => 'key',
                    'value' => $sg_id
                ),
                'sgg_group' => array(
                    'title' => 'Name',
                    'type' => 'text'
                ),
            ),
            'global_actions' => array(
                'clear_session' => array(
                    'title' => 'Release '.$sg_name,
                    'action' => function($query)
                    {
                        Session::forget('goods_id');
                        return redirect('/admin/spkorea_goods_groups');
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
            'title' => 'Option Groups',
            'single' => 'Group',
            'model' => App\Models\SpkoreaGoodsGroup::class,
            'columns' => array(
                'sgg_id' => array(
                    'title' => 'ID'
                ),
                'sg_id' => array(
                    'title' => 'Goods',
                    'relationship' => 'goods',
                    'select' => '(:table).sg_name'
                ),
                'sgg_group' => array(
                    'title' => 'Name'
                ),
                'sgg_required' => array(
                    'title' => 'Essential',
                    'output' => function ($value) {
                        if ($value) {
                            return 'TRUE';
                        }
                        return 'FALSE';
                    }
                )

            ),
            'edit_fields' => array(
                'sgg_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                ),
                'goods' => array(
                    'type' => 'relationship',
                    'title' => 'Goods',
                    'name_field' => 'sg_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array('CONCAT(sg_id, " - ", sg_name)'),
                    'editable' => function ($model) {
                        return !$model->exists;
                    }
                ),
                'sgg_group' => array(
                    'title' => 'Name',
                    'type' => 'text',
                    'limit' => 20
                ),
                'sgg_required' => array(
                    'title' => 'Essential',
                    'type' => 'bool'
                )

            ),
            'actions' => array(
                'add_option' => array(
                    'title' => 'add options',
                    'action' => function ($model) {
                        Session::put('group_id', $model->sgg_id);
                        return redirect('/admin/spkorea_goods_options/new');
                    }
                ),
            ),
            'filters' => array(
                'sg_id' => array(
                    'title' => 'Goods ID',
                    'type' => 'key'
                ),
                'sgg_group' => array(
                    'title' => 'Name',
                    'type' => 'text'
                ),
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
