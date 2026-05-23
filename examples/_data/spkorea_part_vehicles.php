<?php
function spkorea_part_vehicles()
{
    return array(
        'title' => 'Vehicles',
        'single' => 'Vehicle',
        'model' => '\App\Models\SpkoreaPartVehicle',
        'columns' => array(
            'spv_id' => array(
                'title' => 'ID'
            ),
            'manufacturer' => array(
                'title' => 'Manufacturer',
                'relationship' => 'manufacturer',
                'select' => 'CONCAT((:table).spm_code, " -  ", (:table).spm_name)'
            ),
            'spv_code' => array(
                'title' => 'Code',
            ),
            'spv_name' => array(
                'title' => 'Name(Kor)'
            ),
            'spv_name_en' => array(
                'title' => 'Name(En)'
            )
        ),
        'edit_fields' => array(
            'spv_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'manufacturer' => array(
                'title' => 'Manufacturer',
                'type' => 'relationship',
                'description' => 'Search by manufacturer code or name.',
                'name_field' => 'spm_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('CONCAT(spm_code, " - ", spm_name)'),
            ),
            'spv_code' => array(
                'title' => 'Code',
                'type' => 'text',
                'limit' => 3
            ),
            'spv_bpno' => array(
                'title' => 'Mapping Code',
                'type' => 'text',
                'limit' => 10,
//                'editable' => function ($model) {
//                    return !$model->exists;
//                }
            ),
            'spv_name' => array(
                'title' => 'Name(Kor)',
                'type' => 'text',
                'limit' => 50
            ),
            'spv_name_en' => array(
                'title' => 'Name(En)',
                'type' => 'text',
                'limit' => 50
            )

        ),
        'action_permissions' => array(
            'delete' => function ($model) {
                return false;
            }
        ),
        'actions' => array(
            'add_bpno' => array(
                'title' => 'add bpnos',
                'action' => function ($model) {
                    Session::put('vehicles_id', $model->spv_id);
                    return redirect('/admin/spkorea_map_bpnos/new');
                }
            )
        ),
//    'sort' => array(
//        'field' => 'spm_id',
//        'direction' => 'asc'
//    ),
        'filters' => array(
            'spv_code' => array(
                'title' => 'Code',
                'type' => 'text'
            ),
            'spv_name' => array(
                'title' => 'Name',
                'type' => 'text'
            ),
        ),
//        'permission'=> function()
//        {
//            $user = auth()->user();
//            if ($user) {
//                return !$user->isSubAdmin();
//            }
//            return false;
//        },
        'form_width' => 450
    );
}
