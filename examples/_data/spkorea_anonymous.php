<?php
function spkorea_anonymous()
{
    return array(
        'title' => 'Anonymous',
        'single' => 'Anonymous',
        'model' => App\Models\SpkoreaAnonymous::class,
        'columns' => array(
            'sa_id' => array(
                'title' => 'ID'
            ),
            'created_at' => array(
                'title' => 'Since'
            ),
            'sa_mail' => array(
                'title' => 'Mail'
            ),
            'sa_pass' => array(
                'title' => 'Pass'
            ),
            'orders' => array(
                'title' => 'Order',
                'relationship' => 'orders',
                'select' => 'COUNT((:table).so_id)',
            )
        ),
        'edit_fields' => array(
            'sa_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'sa_mail' => array(
                'title' => 'Mail',
                'type' => 'text',
                'limit' => 100
            ),
            'password' => array(
                'title' => 'password',
                'type' => 'password',
                'limit' => 20,
                'editable' => function ($model) {
                    return !$model->exists;
                }
            ),
        ),
        'actions' => array(

        ),
        'filters' => array(
            'sa_id' => array(
                'title' => 'Anonymous ID',
                'type' => 'key'
            ),
            'sa_mail' => array(
                'title' => 'Mail',
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
