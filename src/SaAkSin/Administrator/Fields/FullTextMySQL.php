<?php
/**
 * Created by SaAkSin.
 * We are ARTGRAMMER.
 * Date: 2019-09-01 Time: 오후 8:11
 */
namespace SaAkSin\Administrator\Fields;

use Illuminate\Database\Query\Builder as QueryBuilder;

class FullTextMySQL extends Field
{
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

		// group by 를 제거함. 대용량시 속도 느려지는 현상있으므로...
		$query->groupBy();

		// 반드시 Binding 파라미터 값을 전달하여야함.
		$query->whereRaw('MATCH('.$this->config->getDataModel()->getTable().'.'.$this->getOption('field_name').') AGAINST(? IN BOOLEAN MODE)', [$this->getOption('value').'*']);
	}
}