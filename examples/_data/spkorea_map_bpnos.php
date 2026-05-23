<?php
function spkorea_map_bpnos()
{
    $spv_id = Session::get('vehicles_id');

    if ($spv_id) {
        $part = \App\Models\SpkoreaPartVehicle::find($spv_id);
        $spv_bpno = $part->spv_bpno;

        return array(
            'title' => 'BPNOs ('.$spv_bpno.')',
            'single' => 'BPNO',
            'model' => App\Models\SpkoreaMapBPNO::class,
            'columns' => array(
                'smb_id' => array(
                    'title' => 'ID'
                ),
                'vehicle' => array(
                    'title' => 'Main BPNO',
                    'relationship' => 'vehicle',
                    'select' => 'CONCAT((:table).spv_name_en, " -  ", (:table).spv_bpno)'
                ),
                'smb_bpno' => array(
                    'title' => 'BPNO'
                )
            ),
            'edit_fields' => array(
                'sgf_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                ),
                'vehicle' => array(
                    'title' => 'Main BPNO',
                    'type' => 'relationship',
                    'value' => $spv_id,
                    'name_field' => 'spv_bpno',
                    'editable' => function ($model) {
                        return !$model->exists;
                    }
                ),
                'smb_bpno' => array(
                    'title' => 'BPNO',
                    'type' => 'text',
                    'limit' => 10
                )
            ),
            'filters' => array(
                'spv_id' => array(
                    'title' => 'Main BPNO ID',
                    'type' => 'key',
                    'value' => $spv_id
                )
            ),
            'global_actions' => array(
                'clear_session' => array(
                    'title' => 'Release '.$spv_bpno,
                    'action' => function($query)
                    {
                        Session::forget('vehicles_id');
                        return redirect('/admin/spkorea_map_bpnos');
                    }
                )
            ),
//            'permission'=> function()
//            {
//                $user = auth()->user();
//                if ($user) {
//                    return !$user->isSubAdmin();
//                }
//                return false;
//            },
            'form_width' => 450
        );

    } else {
        return array(
            'title' => 'BPNOs',
            'single' => 'BPNO',
            'model' => App\Models\SpkoreaMapBPNO::class,
            'columns' => array(
                'smb_id' => array(
                    'title' => 'ID'
                ),
                'vehicle' => array(
                    'title' => 'Main BPNO',
                    'relationship' => 'vehicle',
                    'select' => 'CONCAT((:table).spv_name_en, " -  ", (:table).spv_bpno)'
                ),
                'smb_bpno' => array(
                    'title' => 'BPNO'
                )
            ),
            'edit_fields' => array(
                'sgf_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                ),
                'vehicle' => array(
                    'type' => 'relationship',
                    'title' => 'Main BPNO',
                    'description' => 'Search by BPNO',
                    'name_field' => 'spv_bpno',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array('CONCAT(spv_name_en, " - ", spv_bpno)'),
                    'editable' => function ($model) {
                        return !$model->exists;
                    }
                ),
                'smb_bpno' => array(
                    'title' => 'BPNO',
                    'type' => 'text',
                    'limit' => 10
                )
            ),
            'filters' => array(
                'spv_id' => array(
                    'title' => 'Main BPNO ID',
                    'type' => 'key',
                )
            ),
//            'permission'=> function()
//            {
//                $user = auth()->user();
//                if ($user) {
//                    return !$user->isSubAdmin();
//                }
//                return false;
//            },
            'form_width' => 450
        );
    }

}
