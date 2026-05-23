<?php
function spkorea_reviews()
{
    return array(
        'title' => 'Reviews',
        'single' => 'Review',
        'model' => App\Models\SpkoreaReview::class,
        'columns' => array(
            'srv_id' => array(
                'title' => 'ID'
            ),
            'srv_point' => array(
                'title' => 'Point',
             ),
            'srv_title' => array(
                'title' => 'Title'
            ),
//            'srv_desc' => array(
//                'title' => 'Review Desc'
//            ),
            'order_id' => array(
                'title' => 'Order ID',
                'relationship' => 'order',
                'select' => 'CONCAT((:table).so_id, " -  ", (:table).so_name)'
            ),
            'order_title' => array(
                'title' => 'Order Title',
                'relationship' => 'order',
                'select' => '(:table).so_desc'
            ),

            'srv_publish' => array(
                'title' => 'Publish review',
                'output' => function($value) {
                    if($value) {
                        return 'O';
                    }
                    return 'X';
                }
            ),
        ),
        'edit_fields' => array(
            'srv_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'order' => array(
                'title' => 'Order',
                'type' => 'relationship',
                'description' => 'Search Order',
                'name_field' => 'so_id',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('CONCAT(so_id)'),
                'editable' => function ($model) {
                    return !$model->exists;
                }
            ),
            'srv_point' => array(
                'title' => 'Point',
                'type' => 'number',
                'decimals' => 1
            ),
            'srv_title' => array(
                'title' => 'Title',
                'type' => 'text',
                'limit' => 255
            ),
            'srv_desc' => array(
                'title' => 'Comments',
                'type' => 'textarea'
            ),
            'srv_publish' => array(
                'title' => 'Publish review',
                'type' => 'bool'
            )
        ),
        'actions' => array(
        ),
        'filters' => array(
            'srv_id' => array(
                'title' => 'Review ID',
                'type' => 'key'
            ),
            'order' => array(
                'title' => 'Order',
                'type' => 'relationship',
                'description' => 'Order ID',
                'name_field' => 'so_id',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('CONCAT(so_id)'),
            ),
            'srv_publish' => array(
                'title' => 'Publish review',
                'type' => 'bool'
            )
        ),
        'form_width' => 450
    );
}
