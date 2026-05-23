<?php
function spkorea_warehouses()
{
    return array(
        'title' => 'Warehouses',
        'single' => 'Warehouse',
        'model' => App\Models\SpkoreaWarehouse::class,
        'columns' => array(
            'sw_id' => array(
                'title' => 'ID'
            ),
            'sw_name' => array(
                'title' => 'name'
            ),
        ),
        'edit_fields' => array(
            'sw_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'sw_name' => array(
                'title' => 'name',
                'type' => 'text',
                'limit' => 100
            ),
            'sw_desc' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'height' => 250
            ),
        ),
        'actions' => array(
        ),
        'filters' => array(
            'sw_id' => array(
                'title' => 'Warehouse ID',
                'type' => 'key'
            ),
            'sw_name' => array(
                'title' => 'name',
                'type' => 'text'
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
