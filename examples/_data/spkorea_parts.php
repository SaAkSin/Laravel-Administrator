<?php
function spkorea_parts()
{
    $si_id = Session::get('issue_id');
    Session::forget('issue_id');

    $sp_id = Session::get('part_id');
    Session::forget('part_id');

    $filter_id = $si_id ? $si_id : $sp_id;

    return array(
        'title' => 'All Parts',
        'single' => 'Part',
        'model' => App\Models\SpkoreaPart::class,
        'is_top_actions' => true,
        'columns' => array(
            'sp_id' => array(
                'title' => 'ID'
            ),
            'manufacturerForAdmin' => array(
                'title' => 'Manufacturer',
                'output' => function ($value) {
                	if($value) return $value->spm_name;
                	else return '';
                }
            ),
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
            'sp_prevent' => array(
                'title' => 'Not Available',
                'type' => 'bool'
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
            'sp_available' => array(
                'title' => 'Available Number',
                'type' => 'number'
            ),
            'sp_weight' => array(
                'title' => 'Weight',
                'type' => 'number',
                'symbol' => 'kg',
                'decimals' => 2
            ),
            'sp_special' => array(
                'title' => 'use special fee',
                'type' => 'bool'
            ),
            'sp_special_fee' => array(
                'title' => 'Special Fee',
                'type' => 'number',
                'symbol' => '$',
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

//        $query->whereRaw('MATCH(sp_no) AGAINST("*" IN BOOLEAN MODE)');
//        dd($query);
        },
//    'sort' => array(
//        'field' => 'spm_id',
//        'direction' => 'asc'
//    ),
        'actions' => array(
            'update_issue' => array(
                'title' => 'to complete issue(Shipping weight).',
                'permission' => function($model) {
                    if(($model->issues()->where('si_status', 'A')->get()->count() > 0) && ($model->sp_weight > 0) && !$model->sp_prevent){
                        return true;
                    }
                    return false;
                },
//                'confirmation' => 'Are you sure?',
                'action' => function($model) {
                    $issues = $model->issues()->where('si_status', 'A')->get();
                    foreach ($issues as $issue) {
                        $issue->si_status = 'C';
                        $issue->save();
                    }

                    return redirect('/admin/spkorea_issues');
                }
            ),
            'update_issue_not_exist' => array(
                'title' => 'to response issue(not exist part).',
                'permission' => function($model) {
                    if(($model->issues()->where('si_status', 'A')->where('si_type', 'PA')->get()->count() > 0) && $model->sp_prevent){
                        return true;
                    }
                    return false;
                },
//                'confirmation' => 'Are you sure?',
                'action' => function($model) {
                    $issues = $model->issues()->where('si_status', 'A')->get();
                    foreach ($issues as $issue) {
                        $issue->si_type = 'PX';
                        $issue->save();
                    }

                    return redirect('/admin/spkorea_issues');
                }
            ),
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
                'value' => $filter_id,
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
            return env('WEB_URL').'/product/parts/'.$model->sp_id.'?no='.$model->sp_no;
        }
    );
}
