<?php
function spkorea_homepage_populars()
{
    return array(
        'title' => 'Popular Categories',
        'single' => 'Popular',
        'model' => 'App\Models\SpkoreaPopularCategory',
        'columns' => array(
            'homepage' => array(
                'title' => 'Title',
                'relationship' => 'homepage',
                'select' => '(:table).sh_title'
            ),
            'category' => array(
                'title' => 'Category Name',
                'relationship' => 'category',
                'select' => '(:table).sy_name'
            ),
            'spy_order' => array(
                'title' => 'Order'
            )
        ),
        'edit_fields' => array(
            'spy_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'homepage' => array(
                'title' => 'Homepage',
                'type' => 'relationship',
                'description' => 'Search by homepage title.',
                'name_field' => 'sh_title',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('sh_title'),
            ),
            'category' => array(
                'title' => 'Category',
                'type' => 'relationship',
                'description' => 'Search by category name.',
                'name_field' => 'sy_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('sy_name'),
                'options_filter' => function ($query) {
                    $query->where('sy_main', '>', 1)->where('sy_sub', '=', 1);
                },
            ),
            'spy_order' => array(
                'title' => 'Order',
                'type' => 'number'
            )

        ),
        'rules' => array(
            'spy_order' => 'required'
        ),
        'messages' => array(
            'spy_order.required' => 'The order field is required',
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
