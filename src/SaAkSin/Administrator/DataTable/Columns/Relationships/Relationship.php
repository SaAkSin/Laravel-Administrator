<?php
namespace SaAkSin\Administrator\DataTable\Columns\Relationships;

use SaAkSin\Administrator\DataTable\Columns\Column;

/**
 * The Column class helps us construct columns from models. It can be used to derive column information from a model, or it can be
 * instantiated to hold information about any given column.
 */
class Relationship extends Column {

	/**
	 * The specific defaults for subclasses to override
	 *
	 * @var array
	 */
	protected $defaults = array(
		'is_related' => true,
		'external' => true
	);

	/**
	 * The relationship-type-specific defaults for the relationship subclasses to override
	 *
	 * @var array
	 */
	protected $relationshipDefaults = array();

	/**
	 * Builds the necessary fields on the object
	 *
	 * @return void
	 */
	public function build()
	{
		$model = $this->config->getDataModel();
		$options = $this->suppliedOptions;
		$this->tablePrefix = $this->db->getTablePrefix();

		$relationship = \Illuminate\Database\Eloquent\Relations\Relation::noConstraints(function() use ($model, $options) {
			return $model->{$options['relationship']}();
		});
		$relevant_model = $model;
		$selectTable = $options['column_name'] . '_' . $this->tablePrefix . $relationship->getRelated()->getTable();

		//set the relationship object so we can use it later
		$this->relationshipObject = $relationship;

		//replace the (:table) with the generated $selectTable
		$options['select'] = str_replace('(:table)', $selectTable, $options['select']);

		$this->suppliedOptions = $options;
	}

	/**
	 * Gets all default values
	 *
	 * @return array
	 */
	public function getDefaults()
	{
		$defaults = parent::getDefaults();

		return array_merge($defaults, $this->relationshipDefaults);
	}

	/**
	 * Gets all default values
	 *
	 * @return array
	 */
	public function getIncludedColumn()
	{
		return array();
	}

	/**
	 * Sets up the existing relationship wheres
	 *
	 * @param \Illuminate\Database\Eloquent\Relations\Relation		$relationship
	 * @param string												$tableAlias
	 * @param string												$pivotAlias
	 * @param string												$pivot
	 *
	 * @return string
	 */
	public function getRelationshipWheres($relationship, $tableAlias, $pivotAlias = null, $pivot = null)
	{
		//get the relationship model
		$relationshipModel = $relationship->getRelated();

		//get the query instance
		$query = $relationship->getQuery()->getQuery();

		// Relation::noConstraints()로 제약을 해제하여 가져왔으나, 
		// 추가적인 Null 조건 등을 명시적으로 필터링하도록 array_filter를 적용합니다.
		$wheres = array_filter($query->wheres, function($where) {
			if (isset($where['type']) && $where['type'] === 'Null') {
				if (strpos($where['column'], 'deleted_at') === false) {
					return false;
				}
			}
			return true;
		});

		$query->wheres = array_values($wheres);

		//iterate over the wheres to properly alias the columns
		foreach ($query->wheres as &$where)
		{
			//alias the where columns
			if (isset($where['column'])) {
				$where['column'] = $this->aliasRelationshipWhere($where['column'], $tableAlias, $pivotAlias, $pivot);
			}
		}

		$sql = $query->toSql();
		$bindings = $query->getRawBindings()['where'] ?? [];
		$split = explode(' where ', $sql);
		
		$whereSql = isset($split[1]) ? $split[1] : '';
		return array($whereSql, $bindings);
	}

	/**
	 * Aliases an existing where column
	 *
	 * @param string	$column
	 * @param string	$tableAlias
	 * @param string	$pivotAlias
	 * @param string	$pivot
	 *
	 * @return string
	 */
	public function aliasRelationshipWhere($column, $tableAlias, $pivotAlias, $pivot)
	{
		//first explode the string on "." in case it was given with the table already included
		$split = explode('.', $column);

		//if the second split item exists, there was a "."
		if (isset($split[1]))
		{
			//if the table name is the pivot table, append the pivot alias
			if ($split[0] === $pivot)
			{
				return $pivotAlias . '.' . $split[1];
			}
			//otherwise append the table alias
			else
			{
				return $tableAlias . '.' . $split[1];
			}
		}
		else
		{
			return $tableAlias . '.' . $column;
		}
	}

}
