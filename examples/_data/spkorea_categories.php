<?php
function spkorea_categories()
{
    return array(
        'title' => 'Categories',
        'single' => 'Category',
        'model' => 'App\Models\SpkoreaCategory',
        'columns' => array(
            'sy_id' => array(
                'title' => 'ID'
            ),
            'sy_image' => array(
                'title' => 'Image',
                'output' => function ($value) {
                    if ($value != '') {
                        return '<center><img src="/img/categories/' . $value . '" height="50"></center>';
                    } else {
                        return '<center>no image</center>';
                    }
                }
            ),
            'sy_status' => array(
                'title' => 'Status',
                'output' => function ($value) {
                    switch ($value) {
                        case 'V0':
                            return 'invisible';
                        case 'V1':
                            return 'visible';
                        default:
                            return 'unknown';
                    }
                }
            ),
            'sy_name' => array(
                'title' => 'Category Name',
            ),
            'maincateogry' => array(
                'title' => 'Main Cateogry',
                'relationship' => 'maincategory',
                'select' => '(:table).sy_name'
            ),
            'subcategory' => array(
                'title' => 'Sub Cateogry',
                'relationship' => 'subcategory',
                'select' => '(:table).sy_name'
            ),
        ),
        'edit_fields' => array(
            'sy_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'maincategory' => array(
                'title' => 'Main Category',
                'type' => 'relationship',
                'description' => 'Search by category name.',
                'name_field' => 'sy_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('sy_name'),
                'options_filter' => function ($query) {
                    $query->where('sy_main', '=', 1)->where('sy_sub', '=', 1);
                },
            ),
            'subcategory' => array(
                'title' => 'Sub Category',
                'type' => 'relationship',
                'description' => 'Search by category name.',
                'name_field' => 'sy_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('sy_name'),
                'options_filter' => function ($query) {
                    $query->where('sy_main', '>', 1)->where('sy_sub', '=', 1)->orWhere('sy_id', '=', 1);
                },
            ),
            'sy_name' => array(
                'title' => 'Category Name',
                'type' => 'text',
                'limit' => 50
            ),
            'sy_status' => array(
                'title' => 'Status',
                'type' => 'enum',
                'options' => array(
                    'V0' => 'invisible',
                    'V1' => 'visible',
                )
            ),
            'sy_image' => array(
                'title' => 'Image (1M limit)',
                'type' => 'image',
                'location' => public_path() . '/img/categories/',
                'naming' => 'random',
                'length' => 20,
                'size_limit' => 1,
            ),
            'sy_desc' => array(
                'title' => 'Cateogry Description',
                'type' => 'textarea',
                'height' => 200
            )
        ),
        'action_permissions' => array(
            'delete' => function ($model) {
                return false;
            }
        ),
        'sort' => array(
            'field' => 'sy_main',
            'direction' => 'desc'
        ),
        'filters' => array(
            'sy_name' => array(
                'title' => 'Category Name',
                'type' => 'text'
            ),
            'maincategory' => array(
                'title' => 'Main Category',
                'type' => 'relationship',
                'description' => 'Search by main category name.',
                'name_field' => 'sy_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('sy_name'),
                'options_filter' => function ($query) {
                    $query->where('sy_main', '=', 1)->where('sy_sub', '=', 1);
                },
                'editable' => function ($model) {
                    return !$model->exists;
                }
            ),

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
}
