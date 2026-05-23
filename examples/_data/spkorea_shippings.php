<?php
function spkorea_shippings()
{
    return array(
        'title' => 'Shippings',
        'single' => 'Shipping',
        'model' => App\Models\SpkoreaShipping::class,
        'columns' => array(
            'ss_id' => array(
                'title' => 'ID'
            ),
            'user' => array(
                'title' => 'User',
                'relationship' => 'user',
                'select' => 'CONCAT((:table).su_name, " -  ", (:table).email)'
            ),
            'ss_company' => array(
                'title' => 'Company'
            ),
            'ss_phone' => array(
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
            'ss_id' => array(
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
            'ss_default' => array(
                'title' => 'Default',
                'type' => 'bool'
            ),
            'ss_name' => array(
                'title' => 'Name',
                'limit' => 100
            ),
            'ss_company' => array(
                'title' => 'Company',
                'limit' => 100
            ),
            'ss_addr1' => array(
                'title' => 'Address #1',
                'limit' => 255
            ),
            'ss_addr2' => array(
                'title' => 'Address #2',
                'limit' => 255
            ),
            'ss_city' => array(
                'title' => 'City',
                'limit' => 100,
            ),
            'ss_state' => array(
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
            'ss_country' => array(
                'title' => 'Country',
                'description' => 'optional',
                'limit' => 100
            ),
            'ss_zipcode' => array(
                'title' => 'Zip',
                'limit' => 10
            ),
            'ss_phone' => array(
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
            'ss_id' => array(
                'title' => 'Shipping ID',
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
