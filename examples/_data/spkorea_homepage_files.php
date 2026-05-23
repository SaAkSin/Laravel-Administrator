<?php
function spkorea_homepage_files()
{

    return array(
        'title' => 'Slide Items',
        'single' => 'Slide Item',
        'model' => 'App\Models\SpkoreaHomepageFile',
        'columns' => array(
            'shf_id' => array(
                'title' => 'Image',
                'output' => function ($value) {
                    $hpf = App\Models\SpkoreaHomepageFile::find($value);
                    if ($hpf->shf_filename != '') {

                        return '<img src="/img/slides/thumbs/' . $hpf->shf_filename . '">';
                    } else {
                        return 'NONE';
                    }
                }
            ),
            'created_at' => array(
                'title' => 'Created at'
            ),
            'updated_at' => array(
                'title' => 'Updated at'
            ),
            'shf_name' => array(
                'title' => 'Name'
            ),
            'shf_link' => array(
                'title' => 'Link'
            ),
            'shf_filename' => array(
                'title' => 'Filename'
            )
        ),
        'edit_fields' => array(
            'shf_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'shf_name' => array(
                'title' => 'Name',
                'type' => 'text',
                'limit' => 50
            ),
            'shf_filename' => array(
                'title' => 'Image (limit 5M)',
                'type' => 'image',
                'description' => 'recommend size: 1600 x 240',
                'location' => public_path() . '/img/slides/',
                'naming' => 'random',
                'length' => 20,
                'size_limit' => 5,
                'sizes' => array(
                    array(100, 100, 'auto', public_path() . '/img/slides/thumbs/', 100)
                )
            ),
            'shf_link' => array(
                'title' => 'Link',
                'type' => 'text',
                'limit' => 255
            ),
            'shf_desc' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'height' => 200
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
