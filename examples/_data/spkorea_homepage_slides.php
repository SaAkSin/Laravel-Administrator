<?php
function spkorea_homepage_slides()
{
    return array(
        'title' => 'Slides',
        'single' => 'Slide',
        'model' => 'App\Models\SpkoreaHomepage',
        'columns' => array(
            'sh_title' => array(
                'title' => 'Title'
            ),
            'sh_shf1' => array(
                'title' => 'Slide #1',
                'output' => function ($value) {
                    if ($value > 0) {
                        $hpf = App\Models\SpkoreaHomepageFile::find($value);
                        return '<img src="/img/slides/thumbs/' . $hpf->shf_filename . '" width="100">';
                    } else {
                        return 'NONE';
                    }
                }
            ),
            'sh_bgcolor1' => array(
                'title' => 'BG#1',
                'output' => function ($value) {
                    return '<div style="background-color: ' . $value . '; width: 100%; height: 20px; border-radius: 2px;"><center><b><span style="color: #fff">' . $value . '</span></b></center></div>';
                }
            ),
            'sh_shf2' => array(
                'title' => 'Slide #2',
                'output' => function ($value) {
                    if ($value > 0) {
                        $hpf = App\Models\SpkoreaHomepageFile::find($value);
                        return '<img src="/img/slides/thumbs/' . $hpf->shf_filename . '" width="100">';
                    } else {
                        return 'NONE';
                    }
                }
            ),
            'sh_bgcolor2' => array(
                'title' => 'BG#2',
                'output' => function ($value) {
                    return '<div style="background-color: ' . $value . '; width: 100%; height: 20px; border-radius: 2px;"><center><b><span style="color: #fff">' . $value . '</span></b></center></div>';
                }
            ),
            'sh_shf3' => array(
                'title' => 'Slide #3',
                'output' => function ($value) {
                    if ($value > 0) {
                        $hpf = App\Models\SpkoreaHomepageFile::find($value);
                        return '<img src="/img/slides/thumbs/' . $hpf->shf_filename . '" width="100">';
                    } else {
                        return 'NONE';
                    }
                }
            ),
            'sh_bgcolor3' => array(
                'title' => 'BG#3',
                'output' => function ($value) {
                    return '<div style="background-color: ' . $value . '; width: 100%; height: 20px; border-radius: 2px;"><center><b><span style="color: #fff">' . $value . '</span></b></center></div>';
                }
            ),
            'sh_shf4' => array(
                'title' => 'Slide #4',
                'output' => function ($value) {
                    if ($value > 0) {
                        $hpf = App\Models\SpkoreaHomepageFile::find($value);
                        return '<img src="/img/slides/thumbs/' . $hpf->shf_filename . '" width="100">';
                    } else {
                        return 'NONE';
                    }
                }
            ),
            'sh_bgcolor4' => array(
                'title' => 'BG#4',
                'output' => function ($value) {
                    return '<div style="background-color: ' . $value . '; width: 100%; height: 20px; border-radius: 2px;"><center><b><span style="color: #fff">' . $value . '</span></b></center></div>';
                }
            ),
            'sh_shf5' => array(
                'title' => 'Slide #5',
                'output' => function ($value) {
                    if ($value > 0) {
                        $hpf = App\Models\SpkoreaHomepageFile::find($value);
                        return '<img src="/img/slides/thumbs/' . $hpf->shf_filename . '" width="100">';
                    } else {
                        return 'NONE';
                    }
                }
            ),
            'sh_bgcolor5' => array(
                'title' => 'BG#5',
                'output' => function ($value) {
                    return '<div style="background-color: ' . $value . '; width: 100%; height: 20px; border-radius: 2px;"><center><b><span style="color: #fff">' . $value . '</span></b></center></div>';
                }
            ),
            'sh_shf6' => array(
                'title' => 'Slide #6',
                'output' => function ($value) {
                    if ($value > 0) {
                        $hpf = App\Models\SpkoreaHomepageFile::find($value);
                        return '<img src="/img/slides/thumbs/' . $hpf->shf_filename . '" width="100">';
                    } else {
                        return 'NONE';
                    }
                }
            ),
            'sh_bgcolor6' => array(
                'title' => 'BG#6',
                'output' => function ($value) {
                    return '<div style="background-color: ' . $value . '; width: 100%; height: 20px; border-radius: 2px;"><center><b><span style="color: #fff">' . $value . '</span></b></center></div>';
                }
            ),
            'sh_shf7' => array(
                'title' => 'Slide #7',
                'output' => function ($value) {
                    if ($value > 0) {
                        $hpf = App\Models\SpkoreaHomepageFile::find($value);
                        return '<img src="/img/slides/thumbs/' . $hpf->shf_filename . '" width="100">';
                    } else {
                        return 'NONE';
                    }
                }
            ),
            'sh_bgcolor7' => array(
                'title' => 'BG#7',
                'output' => function ($value) {
                    return '<div style="background-color: ' . $value . '; width: 100%; height: 20px; border-radius: 2px;"><center><b><span style="color: #fff">' . $value . '</span></b></center></div>';
                }
            ),
        ),
        'edit_fields' => array(
            'sh_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'sh_title' => array(
                'title' => 'Title',
                'type' => 'text',
                'editable' => false,
                'limit' => 50
            ),
            'image1' => array(
                'title' => 'Slide #1',
                'type' => 'relationship',
                'name_field' => 'shf_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('shf_name')
            ),
            'sh_bgcolor1' => array(
                'title' => 'BG Color#1',
                'type' => 'color',
                'limit' => 7
            ),
            'image2' => array(
                'title' => 'Slide #2',
                'type' => 'relationship',
                'name_field' => 'shf_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('shf_name')
            ),
            'sh_bgcolor2' => array(
                'title' => 'BG Color#2',
                'type' => 'color',
                'limit' => 7
            ),
            'image3' => array(
                'title' => 'Slide #3',
                'type' => 'relationship',
                'name_field' => 'shf_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('shf_name')
            ),
            'sh_bgcolor3' => array(
                'title' => 'BG Color#3',
                'type' => 'color',
                'limit' => 7
            ),
            'image4' => array(
                'title' => 'Slide #4',
                'type' => 'relationship',
                'name_field' => 'shf_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('shf_name')
            ),
            'sh_bgcolor4' => array(
                'title' => 'BG Color#4',
                'type' => 'color',
                'limit' => 7
            ),
            'image5' => array(
                'title' => 'Slide #5',
                'type' => 'relationship',
                'name_field' => 'shf_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('shf_name')
            ),
            'sh_bgcolor5' => array(
                'title' => 'BG Color#5',
                'type' => 'color',
                'limit' => 7
            ),
            'image6' => array(
                'title' => 'Slide #6',
                'type' => 'relationship',
                'name_field' => 'shf_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('shf_name')
            ),
            'sh_bgcolor6' => array(
                'title' => 'BG Color#6',
                'type' => 'color',
                'limit' => 7
            ),
            'image7' => array(
                'title' => 'Slide #7',
                'type' => 'relationship',
                'name_field' => 'shf_name',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('shf_name')
            ),
            'sh_bgcolor7' => array(
                'title' => 'BG Color#7',
                'type' => 'color',
                'limit' => 7
            ),
            'sh_desc' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'height' => 200
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
