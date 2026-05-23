<?php
function spkorea_brands()
{
    return array(
        'title' => 'Brands',
        'single' => 'Brand',
        'model' => App\Models\SpkoreaBrand::class,
        'columns' => array(
            'sd_id' => array(
                'title' => 'ID'
            ),
            'sd_order' => array(
                'title' => 'Order'
            ),
            'image' => array(
                'title' => 'Image'
            ),
            'sd_name' => array(
                'title' => 'Name'
            ),
            'sd_link' => array(
                'title' => 'Link'
            ),
        ),
        'edit_fields' => array(
            'sd_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'sd_order' => array(
                'title' => 'Order',
                'type' => 'number'
            ),
            'sd_name' => array(
                'title' => 'Name',
                'type' => 'text',
                'limit' => 50
            ),
            'sd_link' => array(
                'title' => 'Link',
                'type' => 'text',
                'limit' => 255
            ),
            'sd_image' => array(
                'title' => 'Image (2M limit)',
                'description' => 'w:160, h:55',
                'type' => 'image',
                'location' => public_path() . '/img/brands/',
                'naming' => 'random',
                'length' => 20,
                'size_limit' => 2,
                'sizes' => array(
                    array(150, 50, 'auto', public_path() . '/img/brands/thumbs/', 100)
                ),
            ),
        ),
        'actions' => array(),
        'sort' => array(
            'field' => 'sd_order',
            'direction' => 'asc'
        ),
        'filters' => array(
            'sd_id' => array(
                'title' => 'Brand ID',
                'type' => 'key'
            ),
            'sd_name' => array(
                'title' => 'Name',
                'type' => 'text'
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
