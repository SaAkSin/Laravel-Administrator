<?php

function spkorea_forwarding_boxes()
{
    $ids = App\Models\SpkoreaForwardingBox::join('spkorea_orders as o', 'spkorea_forwarding_boxes.so_id', '=', 'o.so_id')->whereNotNull('spkorea_forwarding_boxes.so_id')->whereIn('o.so_delivery_status', ['C', 'D'])->pluck('spkorea_forwarding_boxes.sfb_id');

    return array(
        'title' => 'Forwarding Boxes',
        'single' => 'Forwarding Box',
        'model' => App\Models\SpkoreaForwardingBox::class,
        'columns' => array(
            'sfb_id' => array(
                'title' => 'ID'
            ),
            'sfb_location' => array(
                'title' => 'Location(SORT)'
            ),
            'sfb_name' => array(
                'title' => 'Name'
            ),
            'order' => array(
                'title' => 'Assigned Order',
                'relationship' => 'order',
                'select' => 'CONCAT((:table).so_delivery_status, " / ", (:table).so_id, " / " , (:table).so_name, "<br/>", (:table).so_desc)',
            )
        ),
        'edit_fields' => array(
            'sfb_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'sfb_location' => array(
                'title' => 'Location(SORT)',
                'editable' => false,
            ),
            'so_id' => array(
                'title' => 'Assigned Order',
                'type' => 'text',
                'editable' => false,
            ),
            'sfb_name' => array(
                'title' => 'Name',
                'type' => 'text',
                'limit' => 100,
            ),
            'sfb_enable' => array(
                'title' => 'Enable',
                'type' => 'bool',
            ),
            'sfb_display' => array(
                'title' => 'Display',
                'type' => 'bool',
            )
        ),
        'actions' => array(
            'unassigned order' => array(
                'title' => 'Unassigned Order',
                'confirmation' => 'Are you sure you want to unassign the order?',
                'permission' => function ($model) {
                    if (!is_null($model->order)) {
                        return ($model->order->so_delivery_status === 'C') || ($model->order->so_delivery_status === 'D');
                    }
                    return false;
                },
                'action' => function ($model) {
                    $model->so_id = null;
                    return $model->save();
                }
            )
        ),
        'filters' => array(
            'sfb_id' => array(
                'title' => 'Forwarding Box ID',
                'type' => 'key'
            ),
            'sfb_display' => array(
                'title' => 'Display',
                'type' => 'bool',
                'value' => true
            )
        ),
        'action_permissions' => array(
            'create' => function ($model) {
                return false;
            },
            'delete' => function ($model) {
                return false;
            }
        ),
        'global_actions' => array(
            'unassigned_orders' => array(
                'title' => 'Unassigned Orders('.count($ids).')',
                'permission' => function($model) use ($ids)
                {
                    return count($ids) > 0;
                },
                'action' => function ($query) use ($ids) {
                    if (count($ids) > 0) {
                        App\Models\SpkoreaForwardingBox::whereIn('sfb_id', $ids)->update(['so_id' => null]);
                        return redirect('/admin/spkorea_forwarding_boxes');
                    }
                }
            )
        ),
        'permission' => function ()
        {
            $user = auth()->user();
            if ($user) {
                return !$user->isSubAdmin();
            }
            return false;
        },
        'sort' => array(
            'field' => 'sfb_location',
            'direction' => 'asc'
        ),
        'form_width' => 450
    );
}
