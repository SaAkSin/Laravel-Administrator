<?php
function spkorea_homepages()
{
    return array(
        'title' => 'Main Pages',
        'single' => 'Page',
        'model' => 'App\Models\SpkoreaHomepage',
        'columns' => array(
            'created_at' => array(
                'title' => 'Created at'
            ),
            'updated_at' => array(
                'title' => 'Modified at'
            ),
            'sh_deploy' => array(
                'title' => 'Status',
                'output' => function ($value) {
                    if ($value) {
                        return 'deploy';
                    } else {
                        return 'not deploy';
                    }
                }
            ),
            'sh_title' => array(
                'title' => 'Title'
            ),
        ),
        'edit_fields' => array(
            'sh_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'sh_title' => array(
                'title' => 'Title',
                'type' => 'text',
                'limit' => 50
            ),
            'sh_deploy' => array(
                'title' => 'Deploy',
                'type' => 'bool'
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
