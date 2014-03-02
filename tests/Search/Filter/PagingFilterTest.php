<?php

namespace Tests\Slice\Search\Filter;



use Slice\Search\Filter\PagingFilter;

/**
 * PagingFilter test case.
 */
class PagingFilterTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var PagingFilter
	 */
	private $PagingFilter;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated PagingFilterTest::setUp()
		
		$this->PagingFilter = new PagingFilter(2, 10);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated PagingFilterTest::tearDown()
		$this->PagingFilter = null;
		
		parent::tearDown ();
	}
	

	/**
	 * Tests PagingFilter->__construct()
	 * @expectedException \InvalidArgumentException
	 */
	public function test__constructWithInvalidCurrentPage() {
		new PagingFilter(-1, 4);
	}
	
	/**
	 * Tests PagingFilter->__construct()
	 * @expectedException \InvalidArgumentException
	 */
	public function test__constructWithInvalidItemsPerPage() {
		new PagingFilter(1, 'string');
	}
	
	/**
	 * Tests PagingFilter->getCurrentPage()
	 */
	public function testGetCurrentPage() {
		$this->assertEquals(2, $this->PagingFilter->getCurrentPage());
	}
	
	/**
	 * Tests PagingFilter->getItemsPerPage()
	 */
	public function testGetItemsPerPage() {
		$this->assertEquals(10, $this->PagingFilter->getItemsPerPage());
	}
	
	/**
	 * Tests PagingFilter->modifyQueryBuilder()
	 */
	public function testModifyQueryBuilder() {
		$qb = $this->getMockBuilder('\Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->getMock();
		
		$qb->expects($this->once())
			->method("setFirstResult")
			->with($this->equalTo(20))
			->will($this->returnValue($qb));
		
		$qb->expects($this->once())
			->method('setMaxResults')
			->with($this->equalTo(10));
		
		
		$fakeResolver = $this->getMock('Slice\Search\AliasResolver\AliasResolverInterface');
		$this->PagingFilter->modifyQueryBuilder($qb, $fakeResolver);
	}
}

