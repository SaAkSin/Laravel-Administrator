<?php
function spkorea_billings()
{
    return array(
        'title' => 'Billings',
        'single' => 'Billing',
        'model' => App\Models\SpkoreaBilling::class,
        'columns' => array(
            'sb_id' => array(
                'title' => 'ID'
            ),
            'user' => array(
                'title' => 'User',
                'relationship' => 'user',
                'select' => 'CONCAT((:table).su_name, " -  ", (:table).email)'
            ),
            'sb_company' => array(
                'title' => 'Company'
            ),
            'sb_phone' => array(
                'title' => 'Phone'
            ),
            'Address' => array(
                'title' => 'Address'
            ),
            'Zip' => array(
                'title' => 'Zip'
            )
        ),
        'edit_fields' => array(
            'sb_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'user' => array(
                'title' => 'User',
                'type' => 'relationship',
                'description' => 'Search Name or E-mail.',
                'name_field' => 'su_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('CONCAT(su_name, " - ", email)'),
                'editable' => function ($model) {
                    return !$model->exists;
                }
            ),
            'sb_default' => array(
                'title' => 'Default',
                'type' => 'bool'
            ),
            'sb_name' => array(
                'title' => 'Name',
                'limit' => 100
            ),
            'sb_company' => array(
                'title' => 'Company',
                'limit' => 100
            ),
            'sb_addr1' => array(
                'title' => 'Address #1',
                'limit' => 255
            ),
            'sb_addr2' => array(
                'title' => 'Address #2',
                'limit' => 255
            ),
            'sb_city' => array(
                'title' => 'City',
                'limit' => 100,
            ),
            'sb_state' => array(
                'title' => 'State',
                'limit' => 100
            ),
            'country' => array(
                'title' => 'Country',
                'type' => 'relationship',
                'name_field' => 'sc_country',
                'autocomplete' => true,
                'num_options' => 10,
            ),
            'sb_country' => array(
                'title' => 'Country',
                'description' => 'optional',
                'limit' => 100
            ),
            'sb_zipcode' => array(
                'title' => 'Zip',
                'limit' => 10
            ),
            'sb_phone' => array(
                'title' => 'Phone',
                'limit' => 25
            )
        ),
        'actions' => array(
//        'view_password' => array(
//            'title' => 'Change password',
//            'action' => function($model)
//            {
//                return Redirect::to('/admin/spkorea_password/'.$model->su_id);
//            }
//        ),
        ),
        'filters' => array(
            'sb_id' => array(
                'title' => 'Billing ID',
                'type' => 'key'
            ),
            'user' => array(
                'title' => 'User',
                'type' => 'relationship',
                'description' => 'Name/E-mail/Phone.',
                'name_field' => 'su_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('CONCAT(su_name, email, su_phone)'),
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
