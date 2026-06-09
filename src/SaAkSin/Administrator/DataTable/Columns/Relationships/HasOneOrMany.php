<?php
namespace SaAkSin\Administrator\DataTable\Columns\Relationships;

class HasOneOrMany extends Relationship {

	/**
	 * Adds selects to a query
	 *
	 * @param array 	$selects
	 *
	 * @return void
	 */
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
		$relatedModel = $relationship->getRelated();
		$from_table = $this->tablePrefix . $relatedModel->getTable();
		$field_table = $columnName . '_' . $from_table;

		//grab the existing where clauses that the user may have set on the relationship
		list($relationshipWheres, $whereBindings) = $this->getRelationshipWheres($relationship, $field_table);

		$subQuery = $relatedModel->newQuery();
		$subQuery->from($from_table . ' AS ' . $field_table);
		$subQuery->select($this->db->raw($this->getOption('select')));

		$subQuery->whereRaw($this->tablePrefix . $relationship->getQualifiedParentKeyName() . ' = ' . $field_table . '.' . $relationship->getForeignKeyName());

		if ($relationshipWheres) {
			$subQuery->whereRaw($relationshipWheres, $whereBindings);
		}

		// SoftDeletes check for related model
		if (method_exists($relatedModel, 'getDeletedAtColumn')) {
			$subQuery->whereNull($field_table . '.' . $relatedModel->getDeletedAtColumn());
		}

		$subQueryBuilder = $subQuery instanceof \Illuminate\Database\Eloquent\Builder ? $subQuery->getQuery() : $subQuery;
		$sql = $subQueryBuilder->toSql();
		$bindings = $subQueryBuilder->getBindings();
		$selects[] = $this->db->raw("({$sql}) AS " . $this->db->getQueryGrammar()->wrap($columnName));
		$query->addBinding($bindings, 'select');
	}
}