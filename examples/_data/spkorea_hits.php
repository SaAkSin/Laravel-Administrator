<?php
function spkorea_hits()
{
    return array(
        'title' => 'HIT Items',
        'single' => 'Part',
        'model' => '\App\Models\SpkoreaPart',
        'columns' => array(
            'sp_id' => array(
                'title' => 'ID'
            ),
            'sp_hitorder' => array(
                'title' => 'Sort Order'
            ),
	        'manufacturerForAdmin' => array(
		        'title' => 'Manufacturer',
		        'output' => function ($value) {
			        if($value) return $value->spm_name;
			        else return '';
		        }
	        ),
//            'manufacturer' => array(
//                'title' => 'Manufacturer',
//                'output' => function ($value) {
//                    return $value->spm_name;
//                }
//            ),
            'vehicle' => array(
                'title' => 'Vehicle',
                'relationship' => 'vehicle',
                'select' => '(:table).spv_name_en'
            ),
            'sp_no' => array(
                'title' => 'Parts Number',
            ),
            'sp_name_en' => array(
                'title' => 'Name'
            ),
            'sp_weight' => array(
                'title' => 'Weight'
            ),
            'sp_money' => array(
                'title' => 'Money',
                'output' => function ($value) {
                    if (!is_null($value)) {
                        return '$' . $value;
                    }
                    return '';
                }
            ),
            'sp_margin' => array(
                'title' => 'Margin',
                'output' => function ($value) {
                    $percent = $value * 100;
                    return $value . " (" . $percent . "%)";
                }
            ),
            'sale' => array(
                'title' => 'Sale',
                'output' => function ($value) {
                    if ($value != 'N/A') {
                        return '$' . $value;
                    }
                    return $value;
                }
            ),
            'sp_hit' => array(
                'title' => 'HIT',
                'output' => function($value) {
                    if($value) {
                        return 'O';
                    }
                    return 'X';
                }
            )
        ),
        'edit_fields' => array(
            'sp_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'vehicle' => array(
                'title' => 'Vehicle',
                'type' => 'relationship',
                'description' => 'Search by vehicle code or name.',
                'name_field' => 'spv_name_en',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('CONCAT(spv_code, " - ", spv_name_en)'),
            ),
            'sp_no' => array(
                'title' => 'Part Number',
                'type' => 'text',
                'limit' => 18,
            ),
            'sp_no_prev' => array(
                'title' => 'Part Number(before)',
                'type' => 'text',
                'limit' => 18,
            ),
            'sp_no_next' => array(
                'title' => 'Part Number(after)',
                'type' => 'text',
                'limit' => 18,
            ),
            'sp_name' => array(
                'title' => 'Name(kor)',
                'type' => 'text',
                'limit' => 30,
            ),
            'sp_name_en' => array(
                'title' => 'Name(en)',
                'type' => 'text',
                'limit' => 30,
            ),
            'sp_weight' => array(
                'title' => 'Weight',
                'type' => 'number',
                'symbol' => 'kg',
                'decimals' => 2
            ),
            'sp_wholesale' => array(
                'title' => 'Wholesale',
                'type' => 'number',
                'limit' => 11
            ),
            'sp_retail' => array(
                'title' => 'Retail',
                'type' => 'number',
                'limit' => 11
            ),
            'sp_retail_vat' => array(
                'title' => 'Retail(VAT)',
                'type' => 'number',
                'limit' => 11
            ),
            'sp_money' => array(
                'title' => 'Money',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
            'sp_margin' => array(
                'title' => 'Margin',
                'type' => 'number',
                'decimals' => 2
            ),
            'sp_hit' => array(
                'title' => 'HIT Item',
                'type' => 'bool'
            ),
            'sp_hitorder' => array(
                'title' => 'HIT Item Sort Order',
                'type' => 'number'
            ),
            'sp_image1' => array(
                'title' => 'Image#1',
                'type' => 'image',
                'location' => public_path() . '/img/parts/',
                'naming' => 'random',
                'length' => 20,
                'size_limit' => 2,
                'sizes' => array(
                    array(150, 150, 'auto', public_path() . '/img/parts/thumbs/', 100)
                )
            ),
            'sp_image2' => array(
                'title' => 'Image#2',
                'type' => 'image',
                'location' => public_path() . '/img/parts/',
                'naming' => 'random',
                'length' => 20,
                'size_limit' => 2,
                'sizes' => array(
                    array(150, 150, 'auto', public_path() . '/img/parts/thumbs/', 100)
                )
            ),
            'sp_image3' => array(
                'title' => 'Image#3',
                'type' => 'image',
                'location' => public_path() . '/img/parts/',
                'naming' => 'random',
                'length' => 20,
                'size_limit' => 2,
                'sizes' => array(
                    array(150, 150, 'auto', public_path() . '/img/parts/thumbs/', 100)
                )
            ),
            'sp_desc' => array(
                'title' => 'Description',
                'type' => 'wysiwyg'
            )
        ),
        'action_permissions' => array(
            'delete' => function ($model) {
                return false;
            }
        ),
        'query_filter' => function ($query) {
            $query->where('sp_hit', true);
        },
        'sort' => array(
            'field' => 'sp_hitorder',
            'direction' => 'desc'
        ),
        'actions' => array(
//            'upload_image' => array(
//                'title' => 'upload images',
//                'action' => function ($model) {
//                    Session::put('part_id', $model->sp_id);
//                    return redirect('/admin/spkorea_part_files/new');
//                }
//            ),
        ),
        'filters' => array(
            'sp_id' => array(
                'title' => 'Part ID',
                'type' => 'key'
            ),
            'sp_no' => array(
                'title' => 'Part Number',
                'type' => 'fulltext_mysql'
            ),
            'sp_name_en' => array(
                'title' => 'Name',
                'type' => 'fulltext_mysql'
            ),
        ),
        'form_width' => 450,
        'permission'=> function()
        {
            $user = auth()->user();
            if ($user) {
                return !$user->isSubAdmin();
            }
            return false;
        },
        'link' => function($model) {
            return env('WEB_URL').'/product/parts/'.$model->sp_id.'?no='.$model->sp_no;
        }
    );
}
