<?php

namespace Tests\Summe\Slice\Filter;

use Summe\Slice\Filter\CriteriaFilter;

use Summe\Slice\Filter\EqualFilter;


/**
 * EqualFilter test case.
 */
class EqualFilterTest extends \PHPUnit_Framework_TestCase {
	
	public function dataProvider() {
		return array(
			array(true, 'Alias.property != :{token}'),
			array(false, 'Alias.property = :{token}')	
		);
	}
	
	
	/**
	 * Tests EqualFilter->getType()
	 */
	public function testGetType() {
		$filter = new EqualFilter('Alias.property', 'val');
		$this->assertEquals('equal', 
				$filter->getType());
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
	public function testEqualsAndInverse($inverse, $expected) 
	{
		$fakeResolver = $this->getMock('Summe\Slice\AliasResolver\AliasResolverInterface');
		
		$fakeResolver->expects($this->any())
		->method('resolveAlias')
		->with('property')
		->will($this->returnValue('Alias.property'));
		
		$filter = new EqualFilter('property', 'value', $inverse);
		
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
			->with($this->getParameterName($filter), 'value');
		
		$filter->modifyQueryBuilder($qb, $fakeResolver);
	}
}

