<?php
function spkorea_part_inqueries()
{
    return array(
        'title' => 'Inqueries',
        'single' => 'Inquery',
        'model' => App\Models\SpkoreaPartInquery::class,
        'columns' => array(
            'spi_id' => array(
                'title' => 'ID',
            ),
            'created_at' => array(
                'title' => 'Date',
            ),
            'spi_status' => array(
                'title' => 'Status',
                'output' => function ($value) {
                    switch ($value) {
                        case 'A':
                            return 'Receipt';
                        case 'C':
                            return 'Complete';
                        default:
                            return 'N/A';
                    }
                }
            ),
            'spi_file1' => array(
                'title' => 'Image#1',
                'output' => function($value) {
                    if($value) {
                        return '<center><img src="/img/inquery/'.$value.'" width="100"></center>';
                    }
                    return '';
                }
            ),
            'spi_file2' => array(
                'title' => 'Image#2',
                'output' => function($value) {
                    if($value) {
                        return '<center><img src="/img/inquery/'.$value.'" width="100"></center>';
                    }
                    return '';
                }
            ),
            'spi_email' => array(
                'title' => 'Email'
            ),
            'spi_year' => array(
                'title' => 'Year',
            ),
            'spi_manufacture' => array(
                'title' => 'Manufacture',
            ),
            'spi_model' => array(
                'title' => 'Model',
            ),
            'spi_vin' => array(
                'title' => 'VIN',
            ),
            'spi_name' => array(
                'title' => 'Name',
            ),
        ),
        'edit_fields' => array(
            'spi_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'spi_year' => array(
                'title' => 'Year',
                'type' => 'text',
                'limit' => 8,
                'editable' => function($model){
                    if($model->exists) {
                        if($model->spi_year) {
                            return false;
                        }
                    }
                    return true;
                }
            ),
            'spi_manufacture' => array(
                'title' => 'Manufacture',
                'type' => 'text',
                'limit' => 20,
                'editable' => function($model){
                    if($model->exists) {
                        if($model->spi_manufacture) {
                            return false;
                        }
                    }
                    return true;
                }
            ),
            'spi_model' => array(
                'title' => 'Model',
                'type' => 'text',
                'limit' => 50,
                'editable' => function($model){
                    if($model->exists) {
                        if($model->spi_model) {
                            return false;
                        }
                    }
                    return true;
                }
            ),
            'spi_vin' => array(
                'title' => 'VIN',
                'type' => 'text',
                'limit' => 17,
                'editable' => function($model){
                    if($model->exists) {
                        if($model->spi_vin) {
                            return false;
                        }
                    }
                    return true;
                }
            ),
            'spi_name' => array(
                'title' => 'Name',
                'type' => 'text',
                'limit' => 30,
                'editable' => function($model){
                    if($model->exists) {
                        if($model->spi_name) {
                            return false;
                        }
                    }
                    return true;
                }
            ),
            'spi_email' => array(
                'title' => 'Email',
                'type' => 'text',
                'limit' => 100,
                'editable' => function($model){
                    if($model->exists) {
                        if($model->spi_email) {
                            return false;
                        }
                    }
                    return true;
                }
            ),
            'spi_status' => array(
                'title' => 'Status',
                'type' => 'enum',
                'options' => array(
                    'A' => 'Receipt',
                    'C' => 'Complete',
                )
            ),
            'spi_file1' => array(
                'title' => 'Image#1',
                'type' => 'image',
                'location' => public_path().'/img/inquery/',
                'naming' => 'random',
                'length' => 20,
                'size_limit' => 1,
                'editable' => function($model){
                    if($model->exists) {
                        if($model->spi_file1) {
                            return false;
                        }
                    }
                    return true;
                }
            ),
            'spi_file2' => array(
                'title' => 'Image#2',
                'type' => 'image',
                'location' => public_path().'/img/inquery/',
                'naming' => 'random',
                'length' => 20,
                'size_limit' => 1,
                'editable' => function($model){
                    if($model->exists) {
                        if($model->spi_file2) {
                            return false;
                        }
                    }
                    return true;
                }
            ),
            'spi_desc' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'height' => 130,
                'editable' => function($model){
                    if($model->exists) {
                        if($model->spi_desc) {
                            return false;
                        }
                    }
                    return true;
                }
            ),
            'spi_memo' => array(
                'title' => 'Memo',
                'type' => 'textarea',
                'height' => 130
            )
        ),
        'actions' => array(),
        'filters' => array(
            'spi_id' => array(
                'title' => 'Inquery ID',
                'type' => 'key'
            ),
            'spi_email' => array(
                'title' => 'Email',
                'type' => 'text'
            )
        ),
        'action_permissions' => array(
            'delete' => function ($model) {
                return false;
            }
        ),
//        'permission'=> function()
//        {
//            $user = auth()->user();
//            if ($user) {
//                return !$user->isSubAdmin();
//            }
//            return false;
//        },
        'form_width' => 450
    );
}
