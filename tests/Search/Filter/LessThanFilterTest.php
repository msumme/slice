<?php

namespace Tests\Slice\Search\Filter;

use Slice\Search\Filter\CriteriaFilter;

use Slice\Search\Filter\LessThanFilter;


/**
 * LessThanFilter test case.
 */
class LessThanFilterTest extends \PHPUnit_Framework_TestCase {
	
	public function dataProvider() {
		return array(
			array(true, 'Alias.property <= :{token}'),
			array(false, 'Alias.property < :{token}')	
		);
	}
	
	
	/**
	 * Tests LessThanFilter->getType()
	 */
	public function testGetType() {
		$filter = new LessThanFilter('Alias.property', 'val');
		$this->assertEquals('lt', $filter->getType());
		
		$filter = new LessThanFilter('Alias.property', 'val', true);
		$this->assertEquals('lte', $filter->getType());
	}
	
	protected function getParameterName(CriteriaFilter $filter) {
		$ref = new \ReflectionMethod(get_class($filter), 'getParameterName');
		$ref->setAccessible(true);
		return $ref->invoke($filter);
	}
	
	/**
	 * @dataProvider dataProvider
	 * @param bool $inverse
	 * @param string $expected
	 */
	public function testEqualsAndInverse($lte, $expected) 
	{
		$fakeResolver = $this->getMock('Slice\Search\AliasResolver\AliasResolverInterface');
		
		$fakeResolver->expects($this->any())
		->method('resolveAlias')
		->with('property')
		->will($this->returnValue('Alias.property'));
		
		
		$filter = new LessThanFilter('property', 1, $lte);
		
		$expected = str_replace('{token}', $this->getParameterName($filter), $expected);
		
		$this->assertEquals($expected, $filter->getDQL($fakeResolver));
		
		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->getMock();
		
		$qb->expects($this->once())
			->method('andWhere')
			->with($filter->getDQL($fakeResolver));
		
		$qb->expects($this->once())
			->method('setParameter')
			->with($this->getParameterName($filter), 1);
		
		$filter->modifyQueryBuilder($qb, $fakeResolver);
	}
}

