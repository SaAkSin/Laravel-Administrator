<?php
function spkorea_exchanges()
{
    return array(
        'title' => 'Exchanges',
        'single' => 'Exchange',
        'model' => App\Models\SpkoreaExchange::class,
        'columns' => array(
            'sx_id' => array(
                'title' => 'ID'
            ),
            'created_at' => array(
                'title' => 'Date'
            ),
            'sx_title' => array(
                'title' => 'Name'
            ),
            'sx_usd' => array(
                'title' => '1 USD to Won',
                'output' => function ($value) {
                    return $value . ' won per $1';
                }
            ),
        ),
        'edit_fields' => array(
            'sx_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'sx_title' => array(
                'title' => 'Name',
                'type' => 'text',
                'limit' => 255
            ),
            'sx_usd' => array(
                'title' => '1 USD to Won',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
            'sx_desc' => array(
                'title' => 'Description',
                'type' => 'textarea'
            )

        ),
        'actions' => array(
            'update_goods' => array(
                'title' => 'update price for all goods',
                'confirmation' => 'Are you sure you want to update?',
                'action' => function ($model) {
                    if ($model->sx_usd > 0) {
                        $count = DB::update('update spkorea_goods set sg_retail = (sg_price/?) where (sg_price > 0) and (sg_auto = true)', [$model->sx_usd]);
                        if ($count > 0) {
                            return true;
                        }
                    }
                    return false;
                }
            ),
            'update_parts' => array(
                'title' => 'update price for all parts',
                'confirmation' => 'Are you sure you want to update?',
                'action' => function ($model) {
                    if ($model->sx_usd > 0) {
                        $count = DB::update('update spkorea_parts set sp_money = (sp_retail/?) where sp_retail > 0', [$model->sx_usd]);
                        if ($count > 0) {
                            return true;
                        }
                    }
                    return false;
                }
            ),
            'update_sp' => array(
                'title' => 'update price for all small packet',
                'confirmation' => 'Are you sure you want to update?',
                'action' => function ($model) {
                    if ($model->sx_usd > 0) {
                        $count = DB::update('update spkorea_cost_sp set scs_usd = (scs_price/?) where scs_price > 0', [$model->sx_usd]);
                        if ($count > 0) {
                            return true;
                        }
                    }
                    return false;
                }
            ),
	        'update_spt' => array(
		        'title' => 'update price for all small packet with tracking',
		        'confirmation' => 'Are you sure you want to update?',
		        'action' => function ($model) {
			        if ($model->sx_usd > 0) {
				        $count = DB::update('update spkorea_cost_spt set scs_usd = (sct_price/?) where sct_price > 0', [$model->sx_usd]);
				        if ($count > 0) {
					        return true;
				        }
			        }
			        return false;
		        }
	        ),
            'update_ap' => array(
                'title' => 'update price for all air parcel',
                'confirmation' => 'Are you sure you want to update?',
                'action' => function ($model) {
                    if ($model->sx_usd > 0) {
                        $count = DB::update('update spkorea_cost_ap set sca_usd = (sca_price/?) where sca_price > 0', [$model->sx_usd]);
                        if ($count > 0) {
                            return true;
                        }
                    }
                    return false;
                }
            ),
            'update_ems' => array(
                'title' => 'update price for all EMS',
                'confirmation' => 'Are you sure you want to update?',
                'action' => function ($model) {
                    if ($model->sx_usd > 0) {
                        $count = DB::update('update spkorea_cost_ems set sce_usd = (sce_price/?) where sce_price > 0', [$model->sx_usd]);
                        if ($count > 0) {
                            return true;
                        }
                    }
                    return false;
                }
            ),
            'update_ups' => array(
                'title' => 'update price for all UPS',
                'confirmation' => 'Are you sure you want to update?',
                'action' => function ($model) {
                    if ($model->sx_usd > 0) {
                        $count = DB::update('update spkorea_cost_ups set scu_usd = (scu_price/?) where scu_price > 0', [$model->sx_usd]);
                        if ($count > 0) {
                            return true;
                        }
                    }
                    return false;
                }
            ),
            'update_dhl' => array(
                'title' => 'update price for all DHL',
                'confirmation' => 'Are you sure you want to update?',
                'action' => function ($model) {
                    if ($model->sx_usd > 0) {
                        $count = DB::update('update spkorea_cost_dhl set scd_usd = (scd_price/?) where scd_price > 0', [$model->sx_usd]);
                        if ($count > 0) {
                            return true;
                        }
                    }
                    return false;
                }
            ),
            'update_fedex' => array(
                'title' => 'update price for all FEDEX',
                'confirmation' => 'Are you sure you want to update?',
                'action' => function ($model) {
                    if ($model->sx_usd > 0) {
                        $count = DB::update('update spkorea_cost_fedex set scf_usd = (scf_price/?) where scf_price > 0', [$model->sx_usd]);
                        if ($count > 0) {
                            return true;
                        }
                    }
                    return false;
                }
            ),
            'update_op' => array(
                'title' => 'update price for all Ocean Post',
                'confirmation' => 'Are you sure you want to update?',
                'action' => function ($model) {
                    if ($model->sx_usd > 0) {
                        $count = DB::update('update spkorea_cost_ocean set sco_usd = (sco_price/?) where sco_price > 0', [$model->sx_usd]);
                        if ($count > 0) {
                            return true;
                        }
                    }
                    return false;
                }
            ),
        ),
        'filters' => array(
            'sx_id' => array(
                'title' => 'Exchange ID',
                'type' => 'key'
            ),
            'sx_title' => array(
                'title' => 'Title',
                'type' => 'text'
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
