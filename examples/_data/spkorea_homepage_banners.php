<?php
function spkorea_homepage_banners()
{
    return array(
        'title' => 'Banners',
        'single' => 'Banner',
        'model' => 'App\Models\SpkoreaHomepageBanner',
        'columns' => array(
            'homepage' => array(
                'title' => 'Title',
                'relationship' => 'homepage',
                'select' => '(:table).sh_title'
            ),
            'shb_start' => array(
                'title' => 'Start Date'
            ),
            'shb_end' => array(
                'title' => 'End Date'
            ),
            'shb_deploy' => array(
                'title' => 'DEPLOY',
                'output' => function($value) {
                    if($value) return 'O';
                    else return 'X';
                }
            ),
            'shb_test' => array(
                'title' => 'TEST',
                'output' => function($value) {
                    if($value) return 'O';
                    else return 'X';
                }
            ),
            'shb_title' => array(
                'title' => 'Title'
            )

        ),
        'edit_fields' => array(
            'shb_id' => array(
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
            'shb_title' => array(
                'title' => 'Title',
                'type' => 'text',
                'limit' => 255
            ),
            'shb_desc' => array(
                'title' => 'Description',
                'type' => 'wysiwyg'
            ),
            'shb_start' => array(
                'title' => 'Start Date',
                'type' => 'date'
            ),
            'shb_end' => array(
                'title' => 'End Date',
                'type' => 'date'
            ),
            'shb_width' => array(
                'title' => 'width',
                'type' => 'number',
                'symbol' => 'px'
            ),
            'shb_height' => array(
                'title' => 'height',
                'type' => 'number',
                'symbol' => 'px'
            ),
            'shb_x' => array(
                'title' => 'X',
                'type' => 'number'
            ),
            'shb_y' => array(
                'title' => 'Y',
                'type' => 'number'
            ),
            'shb_deploy' => array(
                'title' => 'DEPLOY',
                'type' => 'bool'
            ),
            'shb_test' => array(
                'title' => 'TEST',
                'type' => 'bool'
            )
        ),
        'global_actions' => array(
            'redirect_banner_test' => array(
                'title' => 'To BANNER TEST Page',
                'action' => function($query)
                {
                    return redirect('/test_banner');
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
}
