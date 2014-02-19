<?php

namespace Tests\Summe\Slice\Filter;


use Summe\Slice\Filter\NullFilter;

/**
 * NullFilter test case.
 */
class NullFilterTest extends \PHPUnit_Framework_TestCase {
	
	public function dataProvider() {
		
		return array(
			array(true, 'Alias.property IS NOT NULL'),
			array(false, 'Alias.property IS NULL')
				);
		
	}
	

	/**
	 * @dataProvider dataProvider
	 */
	public function testBasicAndInverse($inverse, $expected) {
		
		$fakeResolver = $this->getMock('Summe\Slice\AliasResolver\AliasResolverInterface');
		
		$fakeResolver->expects($this->any())
		->method('resolveAlias')
		->with('property')
		->will($this->returnValue('Alias.property'));
		
		$filter = new NullFilter('property', $inverse);
		
		$this->assertEquals($expected, $filter->getDQL($fakeResolver));
		
		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->getMock();
		
		$qb->expects($this->once())
			->method('andWhere')
			->with($filter->getDQL($fakeResolver));
		
		$filter->modifyQueryBuilder($qb, $fakeResolver);
		
	}
	
	
	
	/**
	 * Tests NullFilter->getType()
	 */
	public function testGetType() {
		$filter = new NullFilter('alias.property');
		$this->assertEquals('null', $filter->getType());
	}
}

