<?php

use App\Rules\NonOverlappingRange;

function spkorea_tax_rules()
{
    // sc_id in session
    $country_id = session()->get('country_id') ?? null;
    if ($country_id) {
        $edit_countries = array(
            'title' => 'Country',
            'type' => 'relationship',
            'name_field' => 'sc_country',
            'value' => $country_id
        );
        $filters = array(
            'country' => array(
                'title' => 'Country',
                'type' => 'relationship',
                'name_field' => 'sc_country',
                'value' => $country_id
            ),
        );
        $global_actions = array(
            'clear_session' => array(
                'title' => 'Release Country',
                'action' => function($query)
                {
                    session()->forget('country_id');
                    return redirect()->to('/admin/spkorea_tax_rules');
                }
            )
        );
    } else {
        $edit_countries = array(
            'title' => 'Country',
            'type' => 'relationship',
            'name_field' => 'sc_country',
            'autocomplete' => true,
            'num_options' => 10,
            'search_fields' => array('sc_country'),
        );
        $filters =array(
            'country' => array(
                'title' => 'Country',
                'type' => 'relationship',
                'name_field' => 'sc_country',
                'autocomplete' => true,
                'num_options' => 10,
                'search_fields' => array('sc_country'),
            ),
        );
        $global_actions = array();
    }

    return array(
        'title' => 'TAX Rules',
        'single' => 'TAX Rule',
        'model' => App\Models\SpkoreaTaxRule::class,
        'columns' => array(
            'str_id' => array(
                'title' => 'ID'
            ),
            'sc_id' => array(
                'title' => 'Country',
                'relationship' => 'country',
                'select' => '(:table).sc_country'
            ),
            'str_min' => array(
                'title' => 'Min',
                'output' => function ($value) {
                    return '>= $'.number_format($value, 2);
                }
            ),
            'str_max' => array(
                'title' => 'Max',
                'output' => function ($value) {
                    return '< $'.number_format($value, 2);
                }
            ),
            'str_tax_rate' => array(
                'title' => 'TAX rate'
            )
        ),
        'edit_fields' => array(
            'str_id' => array(
                'title' => 'ID',
                'type' => 'key'
            ),
            'country' => $edit_countries,
            'str_min' => array(
                'title' => 'min(more than, 이상)',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
            'str_max' => array(
                'title' => 'max(less than, 미만)',
                'type' => 'number',
                'symbol' => '$',
                'decimals' => 2
            ),
            'str_tax_rate' => array(
                'title' => 'TAX rate',
                'type' => 'number',
                'symbol' => '%',
                'decimals' => 2
            ),
        ),
        'actions' => array(

        ),
        'filters' => $filters,
        'global_actions' => $global_actions,
        'rules' => array(
            'sc_id' => 'required',
            'str_min' => [
                'required',
                'numeric',
                new NonOverlappingRange
            ],
            'str_max' => [
                'required',
                'numeric'
            ],
            'str_tax_rate'  => [
                'required',
                'numeric',
                'between:0,100'
            ],
        ),
        'messages' => array(
            'sc_id.required' => 'Please select a country.',
            'str_min.required' => 'Please enter the min value.',
            'str_max.required' => 'Please enter the max value.',
            'str_tax_rate.required' => 'Please enter the TAX rate.'
        ),
        'form_width' => 450
    );
}
