<?php
function spkorea_forwarding_statement_items()
{
    return array(
        'title' => 'Forwarding Statement Items',
        'single' => 'Forwarding Statement Item',
        'model' => App\Models\SpkoreaForwardingStatementItem::class,
        'columns' => array(
            'sfi_id' => array(
                'title' => 'ID'
            ),
            'forwarding_statement' => array(
                'title' => 'Forwarding statement',
                'relationship' => 'forwarding_statement',
                'select' => 'CONCAT((:table).sfs_id, " - ", (:table).created_at)'
            ),
            'warehouse_statement_item' => array(
                'title' => 'Warehouse statement Item',
                'relationship' => 'warehouse_statement_item',
                'select' => 'CONCAT((:table).swi_id)'
            ),
        ),
        'edit_fields' => array(
            'sfi_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'sfs_id' => array(
                'title' => 'Forwarding statement',
                'type' => 'text',
                'editable' => false
            ),
            'swi_id' => array(
                'title' => 'Warehouse statement Item',
                'type' => 'text',
                'editable' => false
            ),
            'sfi_desc' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'height' => 250
            ),
        ),
        'actions' => array(
        ),
        'filters' => array(
            'sfi_id' => array(
                'title' => 'Warehouse Statement ID',
                'type' => 'key'
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
