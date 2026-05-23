<?php
namespace SaAkSin\Administrator\Fields\Relationships;

use Illuminate\Database\Query\Builder as QueryBuilder;

class HasMany extends HasOneOrMany {

	/**
	 * The relationship-type-specific defaults for the relationship subclasses to override
	 *
	 * @var array
	 */
	protected $relationshipDefaults = array(
		'column2' => '',
		'multiple_values' => true,
		'sort_field' => false,
	);


	/**
	 * Fill a model with input data
	 *
	 * @param \Illuminate\Database\Eloquent\Model	$model
	 * @param mixed									$input
	 *
	 * @return array
	 */
	public function fillModel(&$model, $input)
	{
		// $input is an array of all foreign key IDs
		//
		// $model is the model for which the above answers should be associated to
		$fieldName = $this->getOption('field_name');
		$input = $input ? explode(',', $input) : array();
		
		// 입력 배열 내 공백 원소 필터링 및 공백 제거
		$input = array_filter(array_map('trim', $input));
		
		$relationship = $model->{$fieldName}();

		// get the plain foreign key so we can set it to null:
		$fkey = $relationship->getForeignKeyName();

		$relatedObjectClass = get_class($relationship->getRelated());

		// 1. 관계에서 탈락한(사용자가 제외한) 항목들만 disassociate/delete 처리합니다.
		foreach($relationship->get() as $related)
		{
			$relatedId = $related->getKey();
			
			// 유지 대상($input)에 포함되어 있지 않은 탈락 모델인 경우에만 격리하여 지웁니다.
			if (!in_array($relatedId, $input))
			{
				try {
					$related->$fkey = null; // disassociate
					$related->save();
				} catch (\Exception $e) {
					// 만약 외래키 컬럼이 NOT NULL 제약 등으로 null 갱신이 불가능할 경우 관계 해제를 위해 해당 모델을 직접 데이터베이스에서 강제 삭제 처리합니다.
					$related->delete();
				}
			}
		}

		// 2. 유지되거나 새로 추가된 관계 모델들의 관계를 주입 및 보존합니다.
		$i = 0;
		foreach($input as $foreign_id)
		{
			$relatedObject = call_user_func($relatedObjectClass .'::find', $foreign_id);
			if ($relatedObject) // 널 세이프 가드 주입
			{
				if ($sortField = $this->getOption('sort_field'))
				{
					$relatedObject->$sortField = $i++;
				}

				$relationship->save($relatedObject);
			}
		}
	}


	/**
	 * Filters a query object with this item's data
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

		//get the values
		$value = $this->getOption('value');
		$table = $this->getOption('table');
		$column = $this->getOption('column');
		$column2 = $this->getOption('column2');

		//if there is no value, return
		if (!$value)
		{
			return;
		}

		$model = $this->config->getDataModel();

		//if the table hasn't been joined yet, join it
		if (!$this->validator->isJoined($query, $table))
		{
			$query->join($table, $model->getTable().'.'.$model->getKeyName(), '=', $column);
		}

		//add where clause
		$query->whereIn($column2, $value);

		//add having clauses
		$query->havingRaw('COUNT(DISTINCT ' . $query->getConnection()->getTablePrefix() . $column2 . ') = ' . count($value));

		//add select field
		if ($selects && !in_array($column2, $selects))
		{
			$selects[] = $column2;
		}
	}

}