<?php

function spkorea_ip_whitelist()
{
    return array(
        'title' => 'IP WhiteList',
        'single' => 'IP',
        'model' => App\Models\SpkoreaIpWhitelist::class,
        'columns' => array(
            'siw_id' => array(
                'title' => 'ID'
            ),
            'siw_ip' => array(
                'title' => 'IP'
            ),
            'siw_desc' => array(
                'title' => 'MEMO'
            )
        ),
        'edit_fields' => array(
            'siw_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'siw_ip' => array(
                'title' => 'IP',
                'limit' => 15
            ),
            'siw_desc' => array(
                'title' => 'MEMO',
                'type' => 'textarea',
                'height' => 130
            )
        ),
        'filters' => array(

        ),
        'permission' => function()
        {
            $user = auth()->user();
            if ($user) {
                return !$user->isSubAdmin();
            }
            return false;
        },
        'form_width' => 450,
    );
}
