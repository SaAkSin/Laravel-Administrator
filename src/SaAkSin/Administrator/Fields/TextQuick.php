<?php
namespace SaAkSin\Administrator\Fields;

use Illuminate\Database\Query\Builder as QueryBuilder;

class TextQuick extends Field {

    /**
     * The specific defaults for subclasses to override
     *
     * @var array
     */
    protected $defaults = array(
        'limit' => 0,
        'height' => 100,
    );

    /**
     * The specific rules for subclasses to override
     *
     * @var array
     */
    protected $rules = array(
        'limit' => 'integer|min:0',
        'height' => 'integer|min:0',
    );

    /**
     * Filters a query object given
     *
     * @param \Illuminate\Database\Query\Builder	$query
     * @param array									$selects
     *
     * @return void
     */
    public function filterQuery(QueryBuilder &$query, &$selects = null)
    {
        //run the parent method
        parent::filterQuery($query, $selects);

        //if there is no value, return
        if ($this->getFilterValue($this->getOption('value'))===false)
        {
            return;
        }

        // 시작하는 단어로 검색
        $query->where($this->config->getDataModel()->getTable().'.'.$this->getOption('field_name'), 'LIKE', $this->getOption('value') . '%');
    }
}