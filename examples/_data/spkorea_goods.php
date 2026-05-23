<?php
function spkorea_goods()
{
    return array(
        'title' => 'Goods',
        'single' => 'Goods',
        'model' => App\Models\SpkoreaGoods::class,
        'columns' => array(
            'sg_id' => array(
                'title' => 'ID'
            ),
            'sg_code' => array(
                'title' => 'Code'
            ),
            'sy_id' => array(
                'title' => 'Category',
                'relationship' => 'category',
                'select' => '(:table).sy_name'
            ),
            'image' => array(
                'title' => 'Image'
            ),
            'sg_name' => array(
                'title' => 'Name'
            ),
            'models' => array(
                'title' => 'Model',
                'relationship' => 'models',
                'select' => 'GROUP_CONCAT((:table).sm_title SEPARATOR "/") sm_title'
            ),
//        'sg_model' => array(
//            'title' => 'Model'
//        ),
            'sg_company' => array(
                'title' => 'Company'
            ),
            'sg_price' => array(
                'title' => 'Price',
                'output' => function($value) {
                    return number_format($value);
                }
            ),
            'sg_retail' => array(
                'title' => 'Price(USD)'
            ),
            'sg_status' => array(
                'title' => 'Status',
                'output' => function ($value) {
                    switch ($value) {
                        case 'S1':
                            return 'on sale';
                        case 'O1':
                            return 'sold out';
                        default:
                            return 'unknown';
                    }
                }
            ),
//            'sg_best' => array(
//                'title' => 'BEST',
//                'output' => function($value) {
//                    if($value) {
//                        return 'O';
//                    }
//                    return 'X';
//                }
//            )
        ),
        'edit_fields' => array(
            'sg_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'sg_code' => array(
                'title' => 'Code',
                'type' => 'text',
                'limit' => 10
            ),
            'category' => array(
                'type' => 'relationship',
                'title' => 'Category',
                'name_field' => 'sy_name',
                'autocomplete' => true,
                'num_options' => 15,
                'search_fields' => array('sy_name')
            ),
            'sg_name' => array(
                'title' => 'Name',
                'type' => 'text',
                'limit' => 255
            ),
            'sg_info' => array(
                'title' => 'Summary',
                'type' => 'text',
                'limit' => 255
            ),
            'models' => array(
                'title' => 'Model',
                'type' => 'relationship',
                'name_field' => 'sm_title',
            ),
//            'sg_model' => array(
//                'title' => 'Model',
//                'type' => 'text',
//                'limit' => 255
//            ),
            'sg_company' => array(
                'title' => 'Company',
                'type' => 'text',
                'limit' => 255
            ),
            'sg_free' => array(
                'title' => 'Free Shipping',
                'type' => 'bool'
            ),
            'sg_auto' => array(
                'title' => 'Auto Exchange',
                'type' => 'bool'
            ),
            'sg_best' => array(
                'title' => 'BEST Item',
                'type' => 'bool'
            ),
            'sg_bestorder' => array(
                'title' => 'BEST Item Sort Order',
                'type' => 'number'
            ),
            'sg_price' => array(
                'title' => 'Price',
                'type' => 'number'
            ),
            'sg_retail' => array(
                'title' => 'Price (USD)',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
            'sg_date' => array(
                'title' => 'Making',
                'type' => 'text',
                'description' => 'ex) YYYYMM',
                'limit' => 8
            ),
            'sg_type' => array(
                'title' => 'Type',
                'type' => 'text',
                'limit' => 50
            ),
            'sg_size' => array(
                'title' => 'Size',
                'type' => 'text',
                'limit' => 50
            ),
            'sg_weight' => array(
                'title' => 'Weight',
                'type' => 'number',
                'symbol' => 'kg',
                'decimals' => 3
            ),
            'sg_point' => array(
                'title' => 'Stars',
                'type' => 'number',
                'decimals' => 1,
                'editable' => false
            ),
            'sg_tag' => array(
                'title' => 'Tag',
                'type' => 'text',
                'limit' => 255
            ),
            'sg_status' => array(
                'title' => 'Status',
                'type' => 'enum',
                'options' => array(
                    'S1' => 'on sale',
                    'O1' => 'sold out'
                )
            ),
            'sg_image' => array(
                'title' => 'Image (2M limit)',
                'type' => 'image',
                'location' => public_path() . '/img/goods/',
                'naming' => 'random',
                'length' => 20,
                'size_limit' => 2,
                'sizes' => array(
                    array(150, 150, 'auto', public_path() . '/img/goods/thumbs/', 100)
                ),
            ),
            'sg_desc' => array(
                'title' => 'Description',
                'type' => 'wysiwyg'
            )

        ),
        'actions' => array(
            'update_goods' => array(
                'title' => 'update price for goods',
                'confirmation' => 'Are you sure you want to update?',
                'action' => function ($model) {
                    $exchange = DB::table('spkorea_exchanges')->orderBy('created_at', 'desc')->first();
                    if (!is_null($exchange)) {
                        if ($exchange->sx_usd > 0) {
                            $model->sg_retail = $model->sg_price / $exchange->sx_usd;
                            return $model->save();
                        }
                    }
                    return false;
                }
            ),
            'upload_image' => array(
                'title' => 'upload images',
                'action' => function ($model) {
                    Session::put('goods_id', $model->sg_id);
                    return redirect('/admin/spkorea_goods_files/new');
                }
            ),
            'add_group' => array(
                'title' => 'add groups',
                'action' => function ($model) {
                    Session::put('goods_id', $model->sg_id);
                    return redirect('/admin/spkorea_goods_groups/new');
                }
            ),
            'add_comments' => array(
                'title' => 'add comments',
                'action' => function ($model) {
                    Session::put('goods_id', $model->sg_id);
                    return redirect('/admin/spkorea_goods_comments/new');
                }
            ),
        ),
        'filters' => array(
            'sg_id' => array(
                'title' => 'Goods ID',
                'type' => 'key'
            ),
            'sg_code' => array(
                'title' => 'Code',
                'type' => 'text'
            ),
            'sg_name' => array(
                'title' => 'Name',
                'type' => 'text'
            ),
            'sg_model' => array(
                'title' => 'Model',
                'type' => 'text'
            ),
        ),
//        'permission'=> function()
//        {
//            $user = auth()->user();
//            if ($user) {
//                return !$user->isSubAdmin();
//            }
//            return false;
//        },
        'form_width' => 450,
        'link' => function($model) {
            $category = $model->category()->first();
            if(!is_null($category)) {
                return env('WEB_URL').'/category/'.$category->sy_id.'/'.$category->sy_main.'/'.$model->sg_id;
            }else {
                return '';
            }
        }
    );
}
