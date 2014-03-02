<?php

namespace Tests\Slice\Search\Filter;

use Doctrine\ORM\Query\Expr\Join;

use Slice\Search\Filter\AndFilter;

use Slice\Search\AliasResolver\AliasResolver;

use Slice\Search\Filter\NullFilter;

use Slice\Search\Filter\PagingFilter;

use Slice\Search\Filter\OrderByFilter;

use Slice\Search\Filter\FilterSet;

/**
 * FilterSet test case.
 */
class FilterSetTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var FilterSet
	 */
	private $FilterSet;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated FilterSetTest::setUp()
		
		$this->FilterSet = new FilterSet();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated FilterSetTest::tearDown()
		$this->FilterSet = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Tests FilterSet->addFilter()
	 */
	public function testAddFilter() {
		
		$orderByFilter1 = new OrderByFilter('property', 'ASC');
		$orderByFilter2 = new OrderByFilter('property2', 'DESC');
		
		$this->FilterSet->addFilter($orderByFilter1);
		$this->FilterSet->addFilter($orderByFilter2);
		
		$pagingFilter = new PagingFilter(0, 1);
		
		$this->FilterSet->addFilter($pagingFilter);
		
		$criteriaFilter1 = new NullFilter('property');
		$criteriaFilter2 = new NullFilter('property2');
		
		$this->FilterSet->addFilter($criteriaFilter1);
		$this->FilterSet->addFilter($criteriaFilter2);
		
		$this->assertAttributeEquals(array($orderByFilter1, $orderByFilter2), 'orderFilters', $this->FilterSet);
		$this->assertAttributeEquals(array($criteriaFilter1, $criteriaFilter2), 'criteriaFilters', $this->FilterSet);
		$this->assertAttributeEquals($pagingFilter, 'pagingFilter', $this->FilterSet);
	}
	
	/**
	 * Tests FilterSet->filterForCount()
	 */
	public function testFilterForCount() {
		
		$this->FilterSet = $this->getMockBuilder('Slice\Search\Filter\FilterSet')
			->setMethods(array('applyFiltersAndJoins'))
			->getMock();
		
		$this->FilterSet->addFilter($criteriaFilter1 = new NullFilter('property'));
		
		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->getMock();
		
		$aliasResolver = $this->getMockBuilder('Slice\Search\AliasResolver\AliasResolver')
			->disableOriginalConstructor()
			->getMock();
		
		$this->FilterSet->expects($this->once())
			->method('applyFiltersAndJoins')
			->with(array($criteriaFilter1), $qb, $aliasResolver);
		
		$this->FilterSet->filterForCount($qb, $aliasResolver);
	}
	
	/**
	 * Tests FilterSet->filterForGetResults()
	 */
	public function testFilterForGetResults() {
		$this->FilterSet = $this->getMockBuilder('Slice\Search\Filter\FilterSet')
			->setMethods(array('applyFiltersAndJoins'))
			->getMock();
		
		$this->FilterSet->addFilter(
				$criteriaFilter1 = new NullFilter('property')
		);
		
		$this->FilterSet->addFilter(
			$orderFilter = new OrderByFilter('property', 'ASC')
		);
		
		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->getMock();
		
		$aliasResolver = $this->getMockBuilder('Slice\Search\AliasResolver\AliasResolver')
			->disableOriginalConstructor()
			->getMock();
		
		$this->FilterSet->expects($this->at(0))
			->method('applyFiltersAndJoins')
			->with(array($criteriaFilter1), $qb, $aliasResolver);
		
		$this->FilterSet->expects($this->at(1))
			->method('applyFiltersAndJoins')
			->with(array($orderFilter), $qb, $aliasResolver);
		
		$this->FilterSet->filterForGetResults($qb, $aliasResolver);
	}
	
	/**
	 * Tests FilterSet->filterForSelectRows()
	 */
	public function testFilterForSelectRows() {
		$this->FilterSet = $this->getMockBuilder('Slice\Search\Filter\FilterSet')
			->setMethods(array('applyFiltersAndJoins'))
			->getMock();
		
		$this->FilterSet->addFilter($criteriaFilter1 = new NullFilter('property'));
		$this->FilterSet->addFilter($orderFilter1 = new OrderByFilter('property', 'ASC'));
		
		$pagingFilter = $this->getMockBuilder('Slice\Search\Filter\PagingFilter')
			->setMethods(array('modifyQueryBuilder'))
			->disableOriginalConstructor()
			->getMock();
		
				
		$this->FilterSet->addFilter($pagingFilter);
		
		
		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->getMock();
		
		$aliasResolver = $this->getMockBuilder('Slice\Search\AliasResolver\AliasResolver')
			->disableOriginalConstructor()
			->getMock();
		
		$pagingFilter->expects($this->once())
			->method('modifyQueryBuilder')
			->with($qb, $aliasResolver);
		
		$this->FilterSet->expects($this->at(0))
			->method('applyFiltersAndJoins')
			->with(array($criteriaFilter1), $qb, $aliasResolver);
		
		$this->FilterSet->expects($this->at(1))
			->method('applyFiltersAndJoins')
			->with(array($orderFilter1), $qb, $aliasResolver);
		
		$this->FilterSet->filterForSelectRows($qb, $aliasResolver);
	}
	
	public function testApplyFiltersAndJoins() 
	{
		$this->FilterSet = $this->getMockBuilder('Slice\Search\Filter\FilterSet')
			->setMethods(array('getPropertyPathsFromFilters', 'applyMissingPropertyJoins'))
			->getMock();

		$orderFilter = $this->getMockBuilder('Slice\Search\Filter\OrderByFilter')
			->setMethods(array('getPropertyPaths', 'modifyQueryBuilder'))
			->disableOriginalConstructor()
			->getMock();
		
		$this->FilterSet->addFilter($orderFilter);
		
		
		$this->FilterSet->expects($this->at(0))
			->method('getPropertyPathsFromFilters')
			->with(array($orderFilter))
			->will($this->returnValue(array('property1')));
		
		$this->FilterSet->expects($this->at(1))
			->method('getPropertyPathsFromFilters')
			->with(array($orderFilter))
			->will($this->returnValue(array('property.path')));
		
		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->setMethods(array('addSelect'))
			->getMock();
		
		$aliasResolver = $this->getMockBuilder('Slice\Search\AliasResolver\AliasResolver')
			->disableOriginalConstructor()
			->setMethods(array('resolveAlias'))
			->getMock();
		
		$qb->expects($this->once())
			->method('addSelect')
			->with('resolvedAlias');
		
		$aliasResolver->expects($this->once())
			->method('resolveAlias')
			->with('property.path')
			->will($this->returnValue('resolvedAlias'));
		
		
		$this->FilterSet->expects($this->once())
			->method('applyMissingPropertyJoins')
			->with($qb, $aliasResolver, array('property1'));
		
		
		$reflection = new \ReflectionObject($this->FilterSet);
		$method = $reflection->getMethod('applyFiltersAndJoins');
		$method->setAccessible(true);
		$method->invoke($this->FilterSet, array($orderFilter), $qb, $aliasResolver);
		
		
		
	}	
	
	public function testGetPropertyPathsFromFilters() 
	{
		
		$firstFilter = new NullFilter('property1');
		$secondFilter = new NullFilter('property2');
		$thirdFilter = new NullFilter('property3');
		$fourthFilter = new NullFilter('property1');
		
		$and1 = new AndFilter(array($firstFilter, $secondFilter));
		
		$and2 = new AndFilter(array($and1, $thirdFilter, $fourthFilter));
		
		$reflection = new \ReflectionObject($this->FilterSet);
		$method = $reflection->getMethod('getPropertyPathsFromFilters');
		$method->setAccessible(true);
		$result = $method->invoke($this->FilterSet, array($and2));
		
		$this->assertEquals(array('property1', 'property2', 'property3'), $result);
	}
	
	public function testApplyMissingPropertyJoins()
	{
		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->setMethods(array('getDQLPart', 'leftJoin'))
			->getMock();
		
		$aliasResolver = $this->getMockBuilder('Slice\Search\AliasResolver\AliasResolver')
			->disableOriginalConstructor()
			->setMethods(array('resolveJoins', 'resolveAlias'))
			->getMock();
		
		$aliasResolver->expects($this->at(0))
			->method('resolveJoins')
			->with('path1')
			->will($this->returnValue(['alias1' => 'join1', 'alias2' => 'join2'] ));
		
		$aliasResolver->expects($this->at(1))
			->method('resolveJoins')
			->with('path2')
			->will($this->returnValue(['alias2' => 'join2', 'alias3' => 'join3'] ));
		
		$aliasResolver->expects($this->once())
			->method('resolveAlias')
			->with('*')
			->will($this->returnValue('primaryIdAlias'));
		
		
		$propertyPaths = array('path1', 'path2');
		
		$join1 = new Join('left', 'join1', 'alias1');
		$join2 = new Join('left', 'join2', 'alias2');
		
		$qb->expects($this->once())
			->method('getDQLPart')
			->with('join')
			->will($this->returnValue(array('primaryIdAlias' => [$join1, $join2])));
		
		$qb->expects($this->once())
			->method('leftJoin')
			->with('join3', 'alias3');
		
		$reflection = new \ReflectionObject($this->FilterSet);
		$method = $reflection->getMethod('applyMissingPropertyJoins');
		$method->setAccessible(true);
		$result = $method->invoke($this->FilterSet, $qb, $aliasResolver, $propertyPaths);
		
	}
	
}

