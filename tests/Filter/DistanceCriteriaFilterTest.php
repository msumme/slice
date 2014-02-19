<?php

namespace Tests\Summe\Slice\Filter;

use Tests\Summe\Slice\BaseMockEntitiesTest;

use Summe\Slice\Filter\DistanceCriteriaFilter;

/**
 * DistanceCriteriaFilter test case.
 */
class DistanceCriteriaFilterTest extends BaseMockEntitiesTest {
	
	/**
	 *
	 * @var DistanceCriteriaFilter
	 */
	private $DistanceCriteriaFilter;
	
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
				
		$this->DistanceCriteriaFilter = new DistanceCriteriaFilter('latitude', 'longitude', array('latitude' =>38.971688, 'longitude' => -94.520874), 100, 'distance');
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated DistanceCriteriaFilterTest::tearDown()
		$this->DistanceCriteriaFilter = null;
		
		parent::tearDown ();
	}
		

	/**
	 * Tests DistanceCriteriaFilter->modifyQueryBuilder()
	 */
	public function testModifyQueryBuilder() {
		
		$fakeResolver = $this->getMock('Summe\Slice\AliasResolver\AliasResolverInterface');
		
		$fakeResolver->expects($this->atLeastOnce())
			->method("resolveAlias")
			->will($this->returnCallback(function($path) {
				if($path == 'latitude') return 'TSSTA0.latitude';
				if($path == 'longitude') return 'TSSTA0.longitude';
			}));
			
		$qb = $this->getMockBuilder('\Doctrine\ORM\QueryBuilder')
			->setMethods(array('andWhere', 'andHaving', 'addSelect'))
			->disableOriginalConstructor()
			->getMock();

		
		$ref = new \ReflectionObject($this->DistanceCriteriaFilter);
		$method = $ref->getMethod('getDistanceCalculationExpression');
		$method->setAccessible(true);
		$expr = $method->invoke($this->DistanceCriteriaFilter, $fakeResolver);
		
		$qb->expects($this->once())
			->method('addSelect')
			->with("$expr AS distance");

		$qb->expects($this->once())
			->method('andWhere')
			->with($this->equalTo($this->DistanceCriteriaFilter->getDQL($fakeResolver)))
			->will($this->returnSelf());
		
		$qb->expects($this->once())
			->method('andHaving')
			->with($this->equalTo("distance < 100 OR distance IS NULL"));
			
		$this->DistanceCriteriaFilter->modifyQueryBuilder($qb, $fakeResolver);
		
		//Test no duplication of select being added.
		$method = $ref->getMethod('addDistanceSelect');
		$method->setAccessible(true);
		$method->invoke($this->DistanceCriteriaFilter, $qb, $fakeResolver);
		
	}
}

