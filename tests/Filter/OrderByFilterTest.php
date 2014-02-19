<?php

namespace Tests\Summe\Slice\Filter;


use Summe\Slice\Filter\OrderByFilter;

/**
 * OrderByFilter test case.
 */
class OrderByFilterTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var OrderByFilter
	 */
	private $OrderByFilter;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated OrderByFilterTest::setUp()
		
		$this->OrderByFilter = new OrderByFilter('property', 'DESC');
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated OrderByFilterTest::tearDown()
		$this->OrderByFilter = null;
		
		parent::tearDown ();
	}
		
	/**
	 * Tests OrderByFilter->__construct()
	 * @expectedException \InvalidArgumentException
	 */
	public function test__constructWithInvalidOrderBy() {
		
		new OrderByFilter('property', 'invalid');
	}
	
	public function test__constructWithVariousAscDescRepresentations() {
		//numeric gt 0 ASC
		$orderBy = new OrderByFilter('alias', 1);
		$this->assertEquals('ASC', $orderBy->getDirection());
		$orderBy = new OrderByFilter('alias', 2);
		$this->assertEquals('ASC', $orderBy->getDirection());
		//numeric lt 0 DESC
		$orderBy = new OrderByFilter('alias', -1);
		$this->assertEquals('DESC', $orderBy->getDirection());
		$orderBy = new OrderByFilter('alias', -2);
		$this->assertEquals('DESC', $orderBy->getDirection());
		//text capital and lowercase ASC
		$orderBy = new OrderByFilter('alias', 'asc');
		$this->assertEquals('ASC', $orderBy->getDirection());
		$orderBy = new OrderByFilter('alias', 'ASC');
		$this->assertEquals('ASC', $orderBy->getDirection());
		//text capital and lowercase ASC
		$orderBy = new OrderByFilter('alias', 'DESC');
		$this->assertEquals('DESC', $orderBy->getDirection());
		$orderBy = new OrderByFilter('alias', 'desc');
		$this->assertEquals('DESC', $orderBy->getDirection());
	}
	
	/**
	 * Tests OrderByFilter->modifyQueryBuilder()
	 */
	public function testModifyQueryBuilder() {
		
		$fakeResolver = $this->getMock('Summe\Slice\AliasResolver\AliasResolverInterface');
		
		$fakeResolver->expects($this->any())
			->method('resolveAlias')
			->with('property')
			->will($this->returnValue('Alias.property'));
		
		$qb = $this->getMockBuilder('\Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->getMock();
		
		$qb->expects($this->once())
			->method("addOrderBy")
			->with($this->equalTo('Alias.property'), $this->equalTo('DESC'));
		
		$this->OrderByFilter->modifyQueryBuilder($qb, $fakeResolver);
	}	
	/**
	 * Tests OrderByFilter->getProperty()
	 */
	public function testGetProperty() {
		
		$this->assertEquals(array('property'), $this->OrderByFilter->getPropertyPaths());
	}
	
	/**
	 * Tests OrderByFilter->getDirection()
	 */
	public function testGetDirection() {
		$this->assertEquals('DESC', $this->OrderByFilter->getDirection());
	}
}

