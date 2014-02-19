<?php

namespace Tests\Summe\Slice\Filter;

use Summe\Slice\Filter\CriteriaFilter;

use Summe\Slice\Filter\LikeFilter;

/**
 * LikeFilter test case.
 */
class LikeFilterTest extends \PHPUnit_Framework_TestCase {
	
	public function dataProvider() {
		return array(
				array('value', true, '*', 'Alias.property NOT LIKE :{token}', '%value%'),
				array('value*', true, '*', 'Alias.property NOT LIKE :{token}', 'value%'),
				array('|porcupine', false, '|','Alias.property LIKE :{token}', '%porcupine'),
		);
	}
	
	/**
	 * @dataProvider dataProvider
	 * @param bool $inverse
	 * @param string $expected
	 */
	public function testVariations($value, $inverse, $character, $expectedDQL, $expectedValue)
	{
		
		$fakeResolver = $this->getMock('Summe\Slice\AliasResolver\AliasResolverInterface');
		
		$fakeResolver->expects($this->any())
		->method('resolveAlias')
		->with('property')
		->will($this->returnValue('Alias.property'));
		
		$filter = new LikeFilter('property', $value, $inverse, $character);
	
		$expected = str_replace('{token}', $this->getParameterName($filter), $expectedDQL);
	
		$this->assertEquals($expected, $filter->getDQL($fakeResolver));
	
		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->getMock();
	
		$qb->expects($this->once())
			->method('andWhere')
			->with($filter->getDQL($fakeResolver));
	
		$qb->expects($this->once())
			->method('setParameter')
			->with($this->getParameterName($filter), $expectedValue);
	
		$filter->modifyQueryBuilder($qb, $fakeResolver);
	}
	

	/**
	 * Tests LikeFilter->getType()
	 */
	public function testGetType() {
		$filter = new LikeFilter('Alias.property', 'val');
		$this->assertEquals('like', $filter->getType());
	
		$filter = new LikeFilter('Alias.property', 'val', true);
		$this->assertEquals('notlike', $filter->getType());
	}
	
	
	protected function getParameterName(CriteriaFilter $filter) {
		$ref = new \ReflectionMethod(get_class($filter), 'getParameterName');
		$ref->setAccessible(true);
		return $ref->invoke($filter);
	}
}

