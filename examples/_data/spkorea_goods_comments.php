<?php
function spkorea_goods_comments()
{
    $sg_id = Session::get('goods_id');

    if ($sg_id) {
        $goods = \App\Models\SpkoreaGoods::find($sg_id);
        $sg_name = $goods->sg_name;
        if(strlen($sg_name) > 10) {
            $sg_name = substr($sg_name, 0, 10).'...';
        }

        return array(
            'title' => 'Comments  ('.$sg_name.')',
            'single' => 'Comment',
            'model' => App\Models\SpkoreaGoodsComment::class,
            'columns' => array(
                'sgc_id' => array(
                    'title' => 'ID'
                ),
                'created_at' => array(
                    'title' => 'Date'
                ),
                'sg_id' => array(
                    'title' => 'Goods',
                    'relationship' => 'goods',
                    'select' => '(:table).sg_name'
                ),
                'su_id' => array(
                    'title' => 'User',
                    'relationship' => 'user',
                    'select' => 'CONCAT((:table).su_name, " (", (:table).email,")")'
                ),
                'sgc_point' => array(
                    'title' => 'Point'
                ),
                'sgc_title' => array(
                    'title' => 'Title'
                ),
            ),
            'edit_fields' => array(
                'sgc_id' => array(
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
                'user' => array(
                    'title' => 'User',
                    'type' => 'relationship',
                    'name_field' => 'su_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array("CONCAT(su_name, ' ', email)")
                ),
                'sgc_point' => array(
                    'title' => 'Point',
                    'type' => 'number',
                    'decimals' => 1
                ),
                'sgc_title' => array(
                    'title' => 'Title',
                    'type' => 'text',
                    'limit' => 255
                ),
                'sgc_desc' => array(
                    'title' => 'Comments',
                    'type' => 'textarea'
                )
            ),
            'actions' => array(
                // 'view_messages' => array(
                // 	'title' => '메시지 보기',
                // 	'action' => function($model)
                // 	{
                // 		Session::put('users_id', $model->du_id);
                // 		return Redirect::to('/admin/doodleit_messages');
                // 	}
                // ),
            ),
            'filters' => array(
                'sg_id' => array(
                    'title' => 'Goods ID',
                    'type' => 'key',
                    'value' => $sg_id
                ),
                'goods' => array(
                    'title' => 'GOODS',
                    'type' => 'relationship',
                    'description' => 'Search by Title',
                    'name_field' => 'sg_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array('CONCAT(sg_id, " - ", sg_name)'),
                    'editable' => function ($model) {
                        return !$model->exists;
                    }
                ),
                'user' => array(
                    'title' => 'Name',
                    'type' => 'relationship',
                    'name_field' => 'su_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array("CONCAT(su_name, ' ', email)")
                ),
            ),
            'global_actions' => array(
                'clear_session' => array(
                    'title' => 'Release '.$sg_name,
                    'action' => function($query)
                    {
                        Session::forget('goods_id');
                        return redirect('/admin/spkorea_goods_comments');
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
    }else {
        return array(
            'title' => 'Comments',
            'single' => 'Comment',
            'model' => App\Models\SpkoreaGoodsComment::class,
            'columns' => array(
                'sgc_id' => array(
                    'title' => 'ID'
                ),
                'created_at' => array(
                    'title' => 'Date'
                ),
                'sg_id' => array(
                    'title' => 'Goods',
                    'relationship' => 'goods',
                    'select' => '(:table).sg_name'
                ),
                'su_id' => array(
                    'title' => 'User',
                    'relationship' => 'user',
                    'select' => 'CONCAT((:table).su_name, " (", (:table).email,")")'
                ),
                'sgc_point' => array(
                    'title' => 'Point'
                ),
                'sgc_title' => array(
                    'title' => 'Title'
                ),
            ),
            'edit_fields' => array(
                'sgc_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                ),
                'goods' => array(
                    'title' => 'Goods',
                    'type' => 'relationship',
                    'name_field' => 'sg_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                ),
                'user' => array(
                    'title' => 'User',
                    'type' => 'relationship',
                    'name_field' => 'su_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array("CONCAT(su_name, ' ', email)")
                ),
                'sgc_point' => array(
                    'title' => 'Point',
                    'type' => 'number',
                    'decimals' => 1
                ),
                'sgc_title' => array(
                    'title' => 'Title',
                    'type' => 'text',
                    'limit' => 255
                ),
                'sgc_desc' => array(
                    'title' => 'Comments',
                    'type' => 'textarea'
                )
            ),
            'actions' => array(
                // 'view_messages' => array(
                // 	'title' => '메시지 보기',
                // 	'action' => function($model)
                // 	{
                // 		Session::put('users_id', $model->du_id);
                // 		return Redirect::to('/admin/doodleit_messages');
                // 	}
                // ),
            ),
            'filters' => array(
                'sg_id' => array(
                    'title' => 'Goods ID',
                    'type' => 'key'
                ),
                'goods' => array(
                    'title' => 'GOODS',
                    'type' => 'relationship',
                    'description' => 'Search by Title',
                    'name_field' => 'sg_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array('CONCAT(sg_id, " - ", sg_name)'),
                    'editable' => function ($model) {
                        return !$model->exists;
                    }
                ),
                'user' => array(
                    'title' => 'Name',
                    'type' => 'relationship',
                    'name_field' => 'su_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array("CONCAT(su_name, ' ', email)")
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
