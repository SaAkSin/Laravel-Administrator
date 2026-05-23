<?php
function spkorea_models()
{
    return array(
        'title' => 'Models',
        'single' => 'Model',
        'model' => App\Models\SpkoreaModel::class,
        'columns' => array(
            'sm_id' => array(
                'title' => 'ID'
            ),
            'created_at' => array(
                'title' => 'Date'
            ),
            'sm_title' => array(
                'title' => 'Name'
            ),
            'sm_desc' => array(
                'title' => 'Description'
            )
        ),
        'edit_fields' => array(
            'sm_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'sm_title' => array(
                'title' => 'Name',
                'type' => 'text',
                'limit' => 100
            ),
            'sm_desc' => array(
                'title' => 'Description',
                'type' => 'textarea'
            )

        ),
        'actions' => array(

        ),
        'filters' => array(
            'sm_id' => array(
                'title' => 'Model ID',
                'type' => 'key'
            ),
            'sm_title' => array(
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
