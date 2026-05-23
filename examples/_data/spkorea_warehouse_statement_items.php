<?php
function spkorea_warehouse_statement_items()
{
    return array(
        'title' => 'Warehouse Statement Items',
        'single' => 'Warehouse Statement Item',
        'model' => App\Models\SpkoreaWarehouseStatementItem::class,
        'columns' => array(
            'swi_id' => array(
                'title' => 'ID'
            ),
            'warehouse_statement' => array(
                'title' => 'Warehouse statement',
                'relationship' => 'warehouse_statement',
                'select' => 'CONCAT((:table).sws_id, " - ", (:table).created_at)'
            ),
            'forwarding_statement_item' => array(
                'title' => 'Forwarding statement item',
                'relationship' => 'forwarding_statement_item',
                'select' => 'CONCAT((:table).sfi_id)'
            ),
            'part' => array(
                'title' => 'Part',
                'relationship' => 'part',
                'select' => 'CONCAT((:table).sp_id, " -  ", (:table).sp_name_en)'
            ),
            'swi_location' => array(
                'title' => 'location'
            ),
        ),
        'edit_fields' => array(
            'swi_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'sws_id' => array(
                'title' => 'Warehouse statement',
                'type' => 'text',
                'editable' => false
            ),
            'sfi_id' => array(
                'title' => 'Forwarding statement item',
                'type' => 'text',
                'editable' => false
            ),
            'part' => array(
                'type' => 'relationship',
                'title' => 'Part',
                'description' => 'Search by Title',
                'name_field' => 'sp_name_en',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('CONCAT(sp_id, " - ", sp_name_en)'),
//                'editable' => function ($model) {
//                    return !$model->exists;
//                }
            ),
            'swi_location' => array(
                'title' => 'location',
                'type' => 'text',
                'limit' => 100
            ),
            'swi_desc' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'height' => 250
            ),
        ),
        'actions' => array(
        ),
        'filters' => array(
            'swi_id' => array(
                'title' => 'Warehouse Statement ID',
                'type' => 'key'
            ),
//            'sw_name' => array(
//                'title' => 'name',
//                'type' => 'text'
//            ),
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
