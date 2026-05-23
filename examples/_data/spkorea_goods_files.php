<?php
function spkorea_goods_files()
{
    $sg_id = Session::get('goods_id');

    if ($sg_id) {
        $goods = \App\Models\SpkoreaGoods::find($sg_id);
        $sg_name = $goods->sg_name;
        if(strlen($sg_name) > 10) {
            $sg_name = substr($sg_name, 0, 10).'...';
        }

        return array(
            'title' => 'Files ('.$sg_name.')',
            'single' => 'File',
            'model' => App\Models\SpkoreaGoodsFile::class,
            'columns' => array(
                'sgf_id' => array(
                    'title' => 'ID'
                ),
                'sgf_type' => array(
                    'title' => 'Type',
                    'output' => function ($value) {
                        switch ($value) {
                            case 'I':
                                return 'image';
                            case 'M':
                                return 'movie';
                            case 'D':
                                return 'doc';
                            default:
                                return 'unknown';
                        }
                    }
                ),
                'sgf_filename' => array(
                    'title' => 'File',
                    'output' => function ($value) {
                        if ($value) {
                            return '<img src="/img/goods/thumbs/' . $value . '" width="100">';
                        }
                        return '';
                    }
                ),
                'sgf_url' => array(
                    'title' => 'URL',
                    'output' => function ($value) {
                        return '<a href="' . $value . '">' . $value . '</a>';
                    }
                ),
                'goods' => array(
                    'title' => 'Goods',
                    'relationship' => 'goods',
                    'select' => 'CONCAT((:table).sg_id, " -  ", (:table).sg_name)'
                ),
            ),
            'edit_fields' => array(
                'sgf_id' => array(
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
                'sgf_filename' => array(
                    'title' => 'File (2M limit)',
                    'type' => 'image',
                    'description' => '400x400: ',
                    'location' => public_path() . '/img/goods/',
                    'naming' => 'random',
                    'length' => 20,
                    'size_limit' => 2,
                    'display_raw_value' => false,
                    'sizes' => array(
                        array(150, 150, 'auto', public_path() . '/img/goods/thumbs/', 100)
                    )
                ),
            ),
            'actions' => array(
                'update_url' => array(
                    'title' => 'update image\'s url',
                    'permission' => function ($model) {
                        return $model->sgf_filename != '';
                    },
                    'action' => function ($model) {
                        $model->sgf_type = 'I';
                        $model->sgf_path = '/home/autoction/html/laravel/public/img/goods/';
                        $model->sgf_url = '/img/goods/' . $model->sgf_filename;
                        return $model->save();
                    }
                ),
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
            ),
            'global_actions' => array(
                'clear_session' => array(
                    'title' => 'Release '.$sg_name,
                    'action' => function($query)
                    {
                        Session::forget('goods_id');
                        return redirect('/admin/spkorea_goods_files');
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
            'title' => 'Files',
            'single' => 'File',
            'model' => App\Models\SpkoreaGoodsFile::class,
            'columns' => array(
                'sgf_id' => array(
                    'title' => 'ID'
                ),
                'sgf_type' => array(
                    'title' => 'Type',
                    'output' => function ($value) {
                        switch ($value) {
                            case 'I':
                                return 'image';
                            case 'M':
                                return 'movie';
                            case 'D':
                                return 'doc';
                            default:
                                return 'unknown';
                        }
                    }
                ),
                'sgf_filename' => array(
                    'title' => 'File',
                    'output' => function ($value) {
                        if ($value) {
                            return '<img src="/img/goods/thumbs/' . $value . '" width="100">';
                        }
                        return '';
                    }
                ),
                'sgf_url' => array(
                    'title' => 'URL',
                    'output' => function ($value) {
                        return '<a href="' . $value . '">' . $value . '</a>';
                    }
                ),
                'goods' => array(
                    'title' => 'Goods',
                    'relationship' => 'goods',
                    'select' => 'CONCAT((:table).sg_id, " -  ", (:table).sg_name)'
                ),
            ),
            'edit_fields' => array(
                'sgf_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                ),
                'goods' => array(
                    'type' => 'relationship',
                    'title' => 'Goods',
                    'description' => 'Search by Title',
                    'name_field' => 'sg_name',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array('CONCAT(sg_id, " - ", sg_name)'),
                    'editable' => function ($model) {
                        return !$model->exists;
                    }
                ),
                'sgf_filename' => array(
                    'title' => 'File (2M limit)',
                    'type' => 'image',
                    'description' => '400x400: ',
                    'location' => public_path() . '/img/goods/',
                    'naming' => 'random',
                    'length' => 20,
                    'size_limit' => 2,
                    'display_raw_value' => false,
                    'sizes' => array(
                        array(150, 150, 'auto', public_path() . '/img/goods/thumbs/', 100)
                    )
                ),
            ),
            'actions' => array(
                'update_url' => array(
                    'title' => 'update image\'s url',
                    'permission' => function ($model) {
                        return $model->sgf_filename != '';
                    },
                    'action' => function ($model) {
                        $model->sgf_type = 'I';
                        $model->sgf_path = '/home/autoction/html/laravel/public/img/goods/';
                        $model->sgf_url = '/img/goods/' . $model->sgf_filename;
                        return $model->save();
                    }
                ),
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
