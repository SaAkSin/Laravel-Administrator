<?php
namespace SaAkSin\Administrator\DataTable\Columns\Relationships;

class BelongsToMany extends Relationship {

	/**
	 * The relationship-type-specific defaults for the relationship subclasses to override
	 *
	 * @var array
	 */
	protected $relationshipDefaults = array(
		'belongs_to_many' => true
	);


	/**
	 * Adds selects to a query
	 *
	 * @param \Illuminate\Database\Eloquent\Builder	$query
	 * @param array 								$selects
	 *
	 * @return void
	 */
	public function filterQuery($query, &$selects)
	{
		$model = $this->config->getDataModel();
		$columnName = $this->getOption('column_name');

		$relationship = \Illuminate\Database\Eloquent\Relations\Relation::noConstraints(function() use ($model) {
			return $model->{$this->getOption('relationship')}();
		});
		$from_table = $this->tablePrefix . $model->getTable();
		$field_table = $columnName . '_' . $from_table;
		$other_model = $relationship->getRelated();
		$other_table = $this->tablePrefix . $other_model->getTable();
		$other_alias = $columnName . '_' . $other_table;
		$other_key = $other_model->getKeyName();
		$int_table = $this->tablePrefix . $relationship->getTable();
		$int_alias = $columnName . '_' . $int_table;
		$column1 = explode('.', $relationship->getQualifiedForeignPivotKeyName());
		$column1 = $column1[1];
		$column2 = explode('.', $relationship->getQualifiedRelatedPivotKeyName());
		$column2 = $column2[1];

		//grab the existing where clauses that the user may have set on the relationship
		list($relationshipWheres, $whereBindings) = $this->getRelationshipWheres($relationship, $other_alias, $int_alias, $int_table);

		$subQuery = $model->newQuery();
		$subQuery->from($from_table . ' AS ' . $field_table);
		$subQuery->select($this->db->raw($this->getOption('select')));

		$subQuery->leftJoin($int_table . ' AS ' . $int_alias, $int_alias . '.' . $column1, '=', $field_table . '.' . $model->getKeyName());

		$subQuery->leftJoin($other_table . ' AS ' . $other_alias, function($join) use ($other_alias, $other_key, $int_alias, $column2, $other_model) {
			$join->on($other_alias . '.' . $other_key, '=', $int_alias . '.' . $column2);
			if (method_exists($other_model, 'getDeletedAtColumn')) {
				$join->whereNull($other_alias . '.' . $other_model->getDeletedAtColumn());
			}
		});

		$subQuery->whereRaw($this->tablePrefix . $model->getTable() . '.' . $model->getKeyName() . ' = ' . $int_alias . '.' . $column1);

		if ($relationshipWheres) {
			$subQuery->whereRaw($relationshipWheres, $whereBindings);
		}

		if (method_exists($model, 'getDeletedAtColumn')) {
			$subQuery->whereNull($field_table . '.' . $model->getDeletedAtColumn());
		}

		list($sql, $bindings) = $query->getQuery()->createSub($subQuery);
		$selects[] = $this->db->raw("({$sql}) AS " . $this->db->getQueryGrammar()->wrap($columnName));
		$query->addBinding($bindings, 'select');
	}

	/**
	 * Gets all default values
	 *
	 * @return array
	 */
	public function getIncludedColumn()
	{
		$model = $this->config->getDataModel();
		$fk = $model->{$this->getOption('relationship')}()->getRelated()->getKeyName();

		return array($fk => $model->getTable() . '.' . $fk);
	}
}