<?php
/**
 * Created by SaAkSin.
 * We are ARTGRAMMER.
 * Date: 2019-04-08 Time: 오후 2:47
 */
function spkorea_margins()
{
	return array(
		'title' => 'Margins',
		'single' => 'Margin',
		'model' => App\Models\SpkoreaMargin::class,
		'columns' => array(
			'sn_id' => array(
				'title' => 'ID'
			),
			'created_at' => array(
				'title' => 'Date'
			),
			'sn_from' => array(
				'title' => 'from retail',
				'output' => function($value) {
					return $value.' won or more';
				}
			),
			'sn_to' => array(
				'title' => 'to retail',
				'output' => function($value) {
					return 'less than '.$value.'won';
				}
			),
			'sn_margin' => array(
				'title' => 'margin',
				'output' => function($value) {
					return $value.' ('.($value*100).'%)';
				}
			)
		),
		'edit_fields' => array(
			'sn_id' => array(
				'title' => 'ID',
				'type' => 'key'
			),
			'sn_from' => array(
				'title' => 'from retail',
				'type' => 'number'
			),
			'sn_to' => array(
				'title' => 'to retail',
				'type' => 'number'
			),
			'sn_margin' => array(
				'title' => 'margin',
				'type' => 'number',
				'symbol' => '%',
				'decimals' => 2
			)
		),
		'actions' => array(
			'update_margins' => array(
				'title' => 'update margin for selected parts',
				'confirmation' => 'Are you sure you want to update?',
				'action' => function ($model) {
					if(($model->sn_from >= 0) && ($model->sn_to > 0) && ($model->sn_margin > 0)) {
						if ($model->sn_to > $model->sn_from) {
							$count = DB::update('update spkorea_parts set sp_margin = ? where (sp_retail >= ?) and (sp_retail < ?)', [$model->sn_margin, $model->sn_from, $model->sn_to]);
							if ($count > 0) {
								return true;
							}
						}
					}
					return false;
				}
			),
		),
		'filters' => array(
			'sn_id' => array(
				'title' => 'Margin ID',
				'type' => 'key'
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
}
