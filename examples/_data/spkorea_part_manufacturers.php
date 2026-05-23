<?php
function spkorea_part_manufacturers()
{
    return array(
        'title' => 'Manufacturers',
        'single' => 'Manufacturer',
        'model' => '\App\Models\SpkoreaPartManufacturer',
        'columns' => array(
            'spm_id' => array(
                'title' => 'ID'
            ),
            'spm_code' => array(
                'title' => 'Code',
            ),
            'spm_name' => array(
                'title' => 'Name'
            ),
        ),
        'edit_fields' => array(
            'spm_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'spm_code' => array(
                'title' => 'Code',
                'type' => 'text',
                'limit' => 2
            ),
            'spm_name' => array(
                'title' => 'Name',
                'type' => 'text',
                'limit' => 20
            ),

        ),
        'action_permissions' => array(
            'delete' => function ($model) {
                return false;
            }
        ),
        'sort' => array(
            'field' => 'spm_id',
            'direction' => 'asc'
        ),
        'filters' => array(
            'spm_name' => array(
                'title' => 'Name',
                'type' => 'text'
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
        'form_width' => 450
    );
}
