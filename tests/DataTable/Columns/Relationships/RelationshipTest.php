<?php
namespace SaAkSin\Administrator\Tests\DataTable\Columns\Relationships;

use Mockery as m;

class RelationshipTest extends \PHPUnit\Framework\TestCase {

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
		$this->column = m::mock('SaAkSin\Administrator\DataTable\Columns\Relationships\Relationship',
											array($this->validator, $this->config, $this->db, $options))->makePartial();
	}

	/**
	 * Tear down function
	 */
	public function tearDown(): void
	{
		m::close();
	}

	public function testBuild()
	{
		$this->config->shouldReceive('getDataModel')->once()->andReturn(m::mock(array('method' => m::mock(array('getRelated' => m::mock(array('getTable' => '')))))));
		$this->db->shouldReceive('getTablePrefix')->once()->andReturn('');
		$this->column->build();
	}

	public function testGetIncludedColumnReturnsEmptyArray()
	{
		$this->assertEquals($this->column->getIncludedColumn(), array());
	}

	public function testGetRelationshipWheres()
	{
		$query = m::mock('Illuminate\Database\Query\Builder');
		$query->shouldReceive('getRawBindings')->once()->andReturn(array('where' => array('binding_val')))
				->shouldReceive('toSql')->once()->andReturn('select * from table where bar = ?');
		$query->wheres = array(array('type' => 'Basic', 'column' => 'bar'));

		$eloquentQuery = m::mock('Illuminate\Database\Eloquent\Builder');
		$eloquentQuery->shouldReceive('getQuery')->once()->andReturn($query);

		$relatedModel = m::mock('Illuminate\Database\Eloquent\Model');

		$relationship = m::mock('Illuminate\Database\Eloquent\Relations\Relation');
		$relationship->shouldReceive('getQuery')->once()->andReturn($eloquentQuery)
						->shouldReceive('getRelated')->once()->andReturn($relatedModel);

		$this->column->shouldReceive('aliasRelationshipWhere')->once()->andReturn('foo');

		$result = $this->column->getRelationshipWheres($relationship, 'fooalias');
		$this->assertEquals($result, array('bar = ?', array('binding_val')));
	}

	public function testAliasRelationshipWhereUnaliasedColumnOtherTable()
	{
		$result = $this->column->aliasRelationshipWhere('column', 'table_alias', 'pivot_alias', 'pivot');
		$this->assertEquals($result, 'table_alias.column');
	}

	public function testAliasRelationshipWhereAliasedColumnOtherTable()
	{
		$result = $this->column->aliasRelationshipWhere('table.column', 'table_alias', 'pivot_alias', 'pivot');
		$this->assertEquals($result, 'table_alias.column');
	}

	public function testAliasRelationshipWhereAliasedColumnPivotTable()
	{
		$result = $this->column->aliasRelationshipWhere('pivot.column', 'table_alias', 'pivot_alias', 'pivot');
		$this->assertEquals($result, 'pivot_alias.column');
	}

}
