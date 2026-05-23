<?php
function spkorea_user_grades()
{
    return array(
        'title' => 'Member Grades',
        'single' => 'MemberGrade',
        'model' => App\Models\SpkoreaUserGrade::class,
        'columns' => array(
            'sug_id' => array(
                'title' => 'ID'
            ),
            'sug_grade' => array(
                'title' => 'Grade',
                'type' => 'number'
            ),
            'sug_discount' => array(
                'title' => 'Discount',
                'output' => function ($value) {
                    $percent = $value * 100;
                    return $value . " (" . $percent . "%)";
                }
            ),
        ),
        'edit_fields' => array(
            'sug_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'sug_grade' => array(
                'title' => 'Grade',
                'type' => 'number'
            ),
            'sug_discount' => array(
                'title' => 'Discount',
                'type' => 'number',
                'decimals' => 2
            ),
        ),
        'actions' => array(
        ),
        'filters' => array(
            'sug_id' => array(
                'title' => 'ID',
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
