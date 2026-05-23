<?php
function spkorea_suppliers()
{
    return array(
        'title' => 'Suppliers',
        'single' => 'Supplier',
        'model' => App\Models\SpkoreaSupplier::class,
        'columns' => array(
            'ssp_id' => array(
                'title' => 'ID'
            ),
            'ssp_name' => array(
                'title' => 'name'
            ),
            'ssp_business_no' => array(
                'title' => 'business no'
            ),
            'ssp_tel' => array(
                'title' => 'tel'
            ),
        ),
        'edit_fields' => array(
            'ssp_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'ssp_name' => array(
                'title' => 'name',
                'type' => 'text',
                'limit' => 50
            ),
            'ssp_business_no' => array(
                'title' => 'business no',
                'type' => 'text',
                'limit' => 20
            ),
            'ssp_tel' => array(
                'title' => 'tel',
                'type' => 'text',
                'limit' => 20
            ),
            'ssp_desc' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'height' => 250
            ),
        ),
        'actions' => array(
        ),
        'filters' => array(
            'ssp_id' => array(
                'title' => 'Supplier ID',
                'type' => 'key'
            ),
            'ssp_name' => array(
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
