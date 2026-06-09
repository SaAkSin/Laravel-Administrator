<?php
namespace SaAkSin\Administrator\Tests\DataTable\Columns\Relationships;

use Mockery as m;

class HasOneOrManySoftDeleteModelStub extends \Illuminate\Database\Eloquent\Model {
	use \Illuminate\Database\Eloquent\SoftDeletes;
}

class HasOneOrManyTest extends \PHPUnit\Framework\TestCase {

	/**
	 * The Validator mock
	 *
	 * @var Mockery
	 */
	protected $validator;

	/**
	 * The Config mock
	 *
	 * @var Mockery
	 */
	protected $config;

	/**
	 * The DB mock
	 *
	 * @var Mockery
	 */
	protected $db;

	/**
	 * The Column mock
	 *
	 * @var Mockery
	 */
	protected $column;

	/**
	 * Set up function
	 */
	public function setUp(): void
	{
		$this->validator = m::mock('SaAkSin\Administrator\Validator');
		$this->config = m::mock('SaAkSin\Administrator\Config\Model\Config');
		$this->db = m::mock('Illuminate\Database\DatabaseManager');

		$options = array('column_name' => 'test', 'relationship' => 'method', 'select' => 'foo');
		$this->column = m::mock('SaAkSin\Administrator\DataTable\Columns\Relationships\HasOneOrMany',
											array($this->validator, $this->config, $this->db, $options))->makePartial();
	}

	/**
	 * Tear down function
	 */
	public function tearDown(): void
	{
		m::close();
	}

	public function testFilterQuery()
	{
		$subQuery = m::mock('Illuminate\Database\Eloquent\Builder');
		$subQuery->shouldReceive('from')->once()->andReturnSelf()
				 ->shouldReceive('select')->once()->andReturnSelf()
				 ->shouldReceive('whereRaw')->twice()->andReturnSelf();

		$relatedModel = m::mock('Illuminate\Database\Eloquent\Model');
		$relatedModel->shouldReceive('getTable')->once()->andReturn('table');
		$relatedModel->shouldReceive('newQuery')->once()->andReturn($subQuery);

		$relationship = m::mock(array(
			'getPlainForeignKey' => '',
			'getForeignKeyName' => '',
			'getQualifiedParentKeyName' => 'table.column',
			'getRelated' => $relatedModel
		));

		$model = m::mock(array('getTable' => 'table', 'getKeyName' => '', 'method' => $relationship));
		$grammar = m::mock('Illuminate\Database\Query\Grammars');
		$grammar->shouldReceive('wrap')->once()->andReturn('');

		$this->config->shouldReceive('getDataModel')->once()->andReturn($model);
		$this->column->shouldReceive('getOption')->times(3)->andReturn('column_name', 'method', 'select')
						->shouldReceive('getRelationshipWheres')->once()->andReturn(array('where_sql', array()));
		$this->db->shouldReceive('raw')->twice()->andReturn('foo')
					->shouldReceive('getQueryGrammar')->once()->andReturn($grammar);

		$dbQuery = m::mock('Illuminate\Database\Query\Builder')->shouldAllowMockingProtectedMethods();
		$dbQuery->shouldReceive('createSub')->once()->with($subQuery)->andReturn(array('sql', array('binding_val')));

		$query = m::mock('Illuminate\Database\Eloquent\Builder');
		$query->shouldReceive('getQuery')->once()->andReturn($dbQuery);
		$query->shouldReceive('addBinding')->once()->with(array('binding_val'), 'select');

		$selects = array();
		$this->column->filterQuery($query, $selects);
		$this->assertEquals($selects, array('foo'));
	}

	public function testFilterQueryWithSoftDeletes()
	{
		$subQuery = m::mock('Illuminate\Database\Eloquent\Builder');
		$subQuery->shouldReceive('from')->once()->andReturnSelf()
				 ->shouldReceive('select')->once()->andReturnSelf()
				 ->shouldReceive('whereRaw')->twice()->andReturnSelf()
				 ->shouldReceive('whereNull')->once()->with('test_table_table.deleted_at')->andReturnSelf();

		$relatedModel = m::mock('SaAkSin\Administrator\Tests\DataTable\Columns\Relationships\HasOneOrManySoftDeleteModelStub');
		$relatedModel->shouldReceive('getTable')->once()->andReturn('table');
		$relatedModel->shouldReceive('newQuery')->once()->andReturn($subQuery);
		$relatedModel->shouldReceive('getDeletedAtColumn')->once()->andReturn('deleted_at');

		$relationship = m::mock(array(
			'getPlainForeignKey' => '',
			'getForeignKeyName' => '',
			'getQualifiedParentKeyName' => 'table.column',
			'getRelated' => $relatedModel
		));

		$model = m::mock(array('getTable' => 'table', 'getKeyName' => '', 'method' => $relationship));
		$grammar = m::mock('Illuminate\Database\Query\Grammars');
		$grammar->shouldReceive('wrap')->once()->andReturn('');

		$this->config->shouldReceive('getDataModel')->once()->andReturn($model);
		$this->column->shouldReceive('getOption')->times(3)->andReturn('test_table', 'method', 'select')
						->shouldReceive('getRelationshipWheres')->once()->andReturn(array('where_sql', array()));
		$this->db->shouldReceive('raw')->twice()->andReturn('foo')
					->shouldReceive('getQueryGrammar')->once()->andReturn($grammar);

		$dbQuery = m::mock('Illuminate\Database\Query\Builder')->shouldAllowMockingProtectedMethods();
		$dbQuery->shouldReceive('createSub')->once()->with($subQuery)->andReturn(array('sql', array('binding_val')));

		$query = m::mock('Illuminate\Database\Eloquent\Builder');
		$query->shouldReceive('getQuery')->once()->andReturn($dbQuery);
		$query->shouldReceive('addBinding')->once()->with(array('binding_val'), 'select');

		$selects = array();
		$this->column->filterQuery($query, $selects);
		$this->assertEquals($selects, array('foo'));
	}

}