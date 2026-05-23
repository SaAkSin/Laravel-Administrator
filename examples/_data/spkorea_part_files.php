<?php
function spkorea_part_files()
{
    $sp_id = Session::get('part_id');

    if ($sp_id) {
        $part = \App\Models\SpkoreaPart::find($sp_id);
        $sp_name = $part->sp_name_en;
        if(strlen($sp_name) > 10) {
            $sp_name = substr($sp_name, 0, 10).'...';
        }

        return array(
            'title' => 'Files ('.$sp_name.')',
            'single' => 'File',
            'model' => App\Models\SpkoreaPartFile::class,
            'columns' => array(
                'spf_id' => array(
                    'title' => 'ID'
                ),
                'spf_type' => array(
                    'title' => 'Type',
                    'output' => function ($value) {
                        switch ($value) {
                            case 'I':
                                return 'image';
                            case 'M':
                                return 'movie';
                            case 'D':
                                return 'doc';
                            default:
                                return 'unknown';
                        }
                    }
                ),
                'spf_filename' => array(
                    'title' => 'File',
                    'output' => function ($value) {
                        if ($value) {
                            return '<img src="/img/parts/thumbs/' . $value . '" width="100">';
                        }
                        return '';
                    }
                ),
                'spf_url' => array(
                    'title' => 'URL',
                    'output' => function ($value) {
                        return '<a href="' . $value . '">' . $value . '</a>';
                    }
                ),
                'part' => array(
                    'title' => 'Part',
                    'relationship' => 'part',
                    'select' => 'CONCAT((:table).sp_id, " -  ", (:table).sp_name_en)'
                ),
            ),
            'edit_fields' => array(
                'spf_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                ),
                'part' => array(
                    'title' => 'Part',
                    'type' => 'relationship',
                    'value' => $sp_id,
                    'name_field' => 'sp_name_en',
                    'editable' => function ($model) {
                        return !$model->exists;
                    }
                ),
                'spf_filename' => array(
                    'title' => 'File (2M limit)',
                    'type' => 'image',
                    'description' => '400x400: ',
                    'location' => public_path() . '/img/parts/',
                    'naming' => 'random',
                    'length' => 20,
                    'size_limit' => 2,
                    'display_raw_value' => false,
                    'sizes' => array(
                        array(150, 150, 'auto', public_path() . '/img/parts/thumbs/', 100)
                    )
                ),
            ),
            'actions' => array(
                'update_url' => array(
                    'title' => 'update image\'s url',
                    'permission' => function ($model) {
                        return $model->spf_filename != '';
                    },
                    'action' => function ($model) {
                        $model->spf_type = 'I';
                        $model->spf_path = '/home/autoction/html/laravel/public/img/parts/';
                        $model->spf_url = '/img/parts/' . $model->spf_filename;
                        return $model->save();
                    }
                ),
            ),
            'filters' => array(
                'sp_id' => array(
                    'title' => 'Part ID',
                    'type' => 'key',
                    'value' => $sp_id
                ),
                'part' => array(
                    'title' => 'Part',
                    'type' => 'relationship',
                    'description' => 'Search by Title',
                    'name_field' => 'sp_name_en',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array('CONCAT(sp_id, " - ", sg_name_en)'),
                    'editable' => function ($model) {
                        return !$model->exists;
                    }
                ),
            ),
            'global_actions' => array(
                'clear_session' => array(
                    'title' => 'Release '.$sp_name,
                    'action' => function($query)
                    {
                        Session::forget('part_id');
                        return redirect('/admin/spkorea_part_files');
                    }
                )
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

    } else {
        return array(
            'title' => 'Files',
            'single' => 'File',
            'model' => App\Models\SpkoreaPartFile::class,
            'columns' => array(
                'spf_id' => array(
                    'title' => 'ID'
                ),
                'spf_type' => array(
                    'title' => 'Type',
                    'output' => function ($value) {
                        switch ($value) {
                            case 'I':
                                return 'image';
                            case 'M':
                                return 'movie';
                            case 'D':
                                return 'doc';
                            default:
                                return 'unknown';
                        }
                    }
                ),
                'spf_filename' => array(
                    'title' => 'File',
                    'output' => function ($value) {
                        if ($value) {
                            return '<img src="/img/parts/thumbs/' . $value . '" width="100">';
                        }
                        return '';
                    }
                ),
                'spf_url' => array(
                    'title' => 'URL',
                    'output' => function ($value) {
                        return '<a href="' . $value . '">' . $value . '</a>';
                    }
                ),
                'part' => array(
                    'title' => 'Part',
                    'relationship' => 'part',
                    'select' => 'CONCAT((:table).sp_id, " -  ", (:table).sp_name_en)'
                ),
            ),
            'edit_fields' => array(
                'spf_id' => array(
                    'title' => 'ID',
                    'type' => 'key'
                ),
                'part' => array(
                    'type' => 'relationship',
                    'title' => 'Part',
                    'description' => 'Search by Title',
                    'name_field' => 'sp_name_en',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array('CONCAT(sp_id, " - ", sp_name_en)'),
                    'editable' => function ($model) {
                        return !$model->exists;
                    }
                ),
                'spf_filename' => array(
                    'title' => 'File (2M limit)',
                    'type' => 'image',
                    'description' => '400x400: ',
                    'location' => public_path() . '/img/parts/',
                    'naming' => 'random',
                    'length' => 20,
                    'size_limit' => 2,
                    'display_raw_value' => false,
                    'sizes' => array(
                        array(150, 150, 'auto', public_path() . '/img/parts/thumbs/', 100)
                    )
                ),
            ),
            'actions' => array(
                'update_url' => array(
                    'title' => 'update image\'s url',
                    'permission' => function ($model) {
                        return $model->spf_filename != '';
                    },
                    'action' => function ($model) {
                        $model->spf_type = 'I';
                        $model->spf_path = '/home/autoction/html/laravel/public/img/parts/';
                        $model->spf_url = '/img/goods/' . $model->spf_filename;
                        return $model->save();
                    }
                ),
            ),
            'filters' => array(
                'sp_id' => array(
                    'title' => 'Part ID',
                    'type' => 'key'
                ),
                'part' => array(
                    'title' => 'Part',
                    'type' => 'relationship',
                    'description' => 'Search by Title',
                    'name_field' => 'sp_name_en',
                    'autocomplete' => true,
                    'num_options' => 10,
                    'search_fields' => array('CONCAT(sp_id, " - ", sp_name_en)'),
                    'editable' => function ($model) {
                        return !$model->exists;
                    }
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

}
