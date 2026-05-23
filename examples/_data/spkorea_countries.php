<?php
function spkorea_countries()
{
	return array(
		'title' => 'Countries & TAX',
		'single' => 'Country',
		'model' => App\Models\SpkoreaCountry::class,
		'columns' => array(
			'sc_id' => array(
				'title' => 'ID'
			),
    		'sc_country' => array(
				'title' => 'Country'
			),
            'sc_order' => array(
                'title' => 'Sort Order'
            ),
			'sc_code' => array(
				'title' => 'Country Code'
			),
            'sc_tax_type' => array(
                'title' => 'TAX basis',
                'output' => function ($value) {
                    switch ($value) {
                        case 'PO':
                            return 'product only';
                        case 'PS':
                            return 'product and shipping';
                        case 'NO':
                            return 'none';
                        default:
                            return 'N/A';
                    }
                }
            ),
            'sc_clearance_fee' => array(
                'title' => 'Fixed Clearance Fee',
                'output' => function ($value) {
                    return '$'.number_format($value, 2);
                }
            ),
			'sc_sp' => array(
				'title' => 'SMALL PACKET'
			),
            'sc_sp_ratio' => array(
                'title' => '(Ratio)'
            ),
			'sc_ap' => array(
				'title' => 'AIR PARCEL'
			),
            'sc_ap_ratio' => array(
                'title' => '(Ratio)'
            ),
			'sc_ems' => array(
				'title' => 'EMS'
			),
            'sc_ems_ratio' => array(
                'title' => '(Ratio)'
            ),
			'sc_ups' => array(
				'title' => 'UPS'
			),
            'sc_ups_ratio' => array(
                'title' => '(Ratio)'
            ),
			'sc_dhl' => array(
				'title' => 'DHL'
			),
            'sc_dhl_ratio' => array(
                'title' => '(Ratio)'
            ),
            'sc_fedex' => array(
                'title' => 'FEDEX'
            ),
            'sc_fedex_ratio' => array(
                'title' => '(Ratio)'
            ),
            'sc_op' => array(
                'title' => 'OCEAN POST'
            ),
            'sc_op_ratio' => array(
                'title' => '(Ratio)'
            )
		),
		'edit_fields' => array(
			'sc_id' => array(
				'title' => 'ID',
				'type' => 'key'
			),
			'sc_country' => array(
				'title' => 'Country',
				'type' => 'text',
				'limit' => 100
			),
			'sc_code' => array(
				'title' => 'Country Code',
				'type' => 'text',
				'limit' => 2
			),
			'sc_sp' => array(
				'title' => 'SMALL PACKET',
				'type' => 'text',
				'limit' => 2
			),
            'sc_sp_ratio' => array(
                'title' => 'SMALL PACKET Ratio',
                'type' => 'number',
                'symbol' => 'x',
                'decimals' => 2
            ),
			'sc_ap' => array(
				'title' => 'AIR PARCEL',
				'type' => 'text',
				'limit' => 2
			),
            'sc_ap_ratio' => array(
                'title' => 'AIR PARCEL Ratio',
                'type' => 'number',
                'symbol' => 'x',
                'decimals' => 2
            ),
			'sc_ems' => array(
				'title' => 'EMS',
				'type' => 'text',
				'limit' => 2
			),
            'sc_ems_ratio' => array(
                'title' => 'EMS Ratio',
                'type' => 'number',
                'symbol' => 'x',
                'decimals' => 2
            ),
			'sc_ups' => array(
				'title' => 'UPS',
				'type' => 'text',
				'limit' => 2
			),
            'sc_ups_ratio' => array(
                'title' => 'UPS Ratio',
                'type' => 'number',
                'symbol' => 'x',
                'decimals' => 2
            ),
			'sc_dhl' => array(
				'title' => 'DHL',
				'type' => 'text',
				'limit' => 2
			),
            'sc_dhl_ratio' => array(
                'title' => 'DHL Ratio',
                'type' => 'number',
                'symbol' => 'x',
                'decimals' => 2
            ),
            'sc_fedex' => array(
                'title' => 'FEDEX',
                'type' => 'text',
                'limit' => 2
            ),
            'sc_fedex_ratio' => array(
                'title' => 'FEDEX Ratio',
                'type' => 'number',
                'symbol' => 'x',
                'decimals' => 2
            ),
            'sc_op' => array(
                'title' => 'OCEAN POST',
                'type' => 'text',
                'limit' => 2
            ),
            'sc_op_ratio' => array(
                'title' => 'OCEAN POST Ratio',
                'type' => 'number',
                'symbol' => 'x',
                'decimals' => 2
            ),
            'sc_tax_type' => array(
                'title' => 'TAX basis',
                'type' => 'enum',
                'options' => array(
                    'PO' => 'product only',
                    'PS' => 'product and shipping',
                    'NO' => 'none'
                )
            ),
            'sc_clearance_fee' => array(
                'title' => 'Fixed Clearance Fee',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
            'sc_tax_message' => array(
                'title' => 'TAX explanation',
                'type' => 'textarea',
                'height' => 130
            ),
            'sc_order' => array(
                'title' => 'SORT ORDER',
                'type' => 'number'
            ),
		),
		'actions' => array(
			 'view_tax' => array(
			 	'title' => 'Managing the Tax Rate',
			 	'action' => function($model)
			 	{
                     session()->put('country_id', $model->sc_id);
                     return redirect()->to('/admin/spkorea_tax_rules');
			 	}
			 ),
		),
		'filters' => array(
			'sc_id' => array(
				'title' => 'Country ID',
				'type' => 'key'
			),
			'sc_country' => array(
				'title' => 'Country',
				'type' => 'text'
			),
			'sc_code' => array(
				'title' => 'Country Code',
				'type' => 'text'
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
