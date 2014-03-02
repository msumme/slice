<?php

namespace Tests\Slice\Search\Filter;

use Tests\Slice\Search\BaseMockEntitiesTest;

use Slice\Search\Filter\DistanceSortFilter;

/**
 * DistanceSortFilter test case.
 */
class DistanceSortFilterTest extends BaseMockEntitiesTest {
	
	/**
	 *
	 * @var DistanceSortFilter
	 */
	private $DistanceSortFilter;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated DistanceSortFilterTest::setUp()
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated DistanceSortFilterTest::tearDown()
		$this->DistanceSortFilter = null;
		
		parent::tearDown ();
	}
		
	/**
	 * Tests DistanceSortFilter->modifyQueryBuilder()
	 */
	public function testModifyQueryBuilder() {
		
		$qb = $this->getMockBuilder('\Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->getMock();
		
		
		$fakeResolver = $this->getMock('Slice\Search\AliasResolver\AliasResolverInterface');		
		
		$fakeResolver->expects($this->atLeastOnce()) 
			->method("resolveAlias")
			->will($this->returnCallback(function($path) {
				if($path == 'latitude') return 'TSSTA0.latitude';
				if($path == 'longitude') return 'TSSTA0.longitude';
			}));
		
		$distSort = new DistanceSortFilter('latitude', 'longitude', array('latitude' =>38.971688, 'longitude' => -94.520874), 'distance', 'ASC');
		
		$ref = new \ReflectionObject($distSort);
		$method = $ref->getMethod('getDistanceCalculationExpression');		
		$method->setAccessible(true);
		$expr = $method->invoke($distSort, $fakeResolver);
		
				
		$qb->expects($this->once())
			->method('addSelect')
			->with("$expr AS distance");
			
		$qb->expects($this->once())
			->method("addOrderBy")
			->with('distance', 'ASC');
		
		$distSort->modifyQueryBuilder($qb, $fakeResolver);
	}
}

