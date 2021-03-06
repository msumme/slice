<?php

namespace Tests\Slice\Search\Filter;

use Slice\Search\Filter\LessThanFilter;

use Slice\Search\Filter\GreaterThanFilter;

use Slice\Search\Filter\AndFilter;

/**
 * AndFilter test case.
 */
class AndFilterTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testArgumentValidation() 
	{
		$filter = new AndFilter(array('not_class'));
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testEmpty() {
		$filter = new AndFilter(array());
	}
	
	
	public function testCombinations() 
	{
		
		$fakeResolver = $this->getMock('Slice\Search\AliasResolver\AliasResolverInterface');
		
		$fakeResolver->expects($this->any())
		->method('resolveAlias')
		->with('property')
		->will($this->returnValue('Alias.property'));
		
		$filters = array();
		
		$filters[] = new GreaterThanFilter('property', 1);
		$filters[] = new LessThanFilter('property', 2);
		
		$filter = new AndFilter($filters);
		
		$parts = array();
		$expectedParams = array();
		foreach($filters as $f) {
			$parts[] = $f->getDQL($fakeResolver);
			$expectedParams = array_merge($expectedParams, $f->getParameters());
		}
		
		$expected = "(". join(' AND ', $parts) .")";
		
		$this->assertEquals($expected, $filter->getDQL($fakeResolver));
		
		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->getMock();
		
		$qb->expects($this->once())
			->method('andWhere')
			->with($filter->getDQL($fakeResolver));
		
		$qb->expects($this->exactly(2))
			->method('setParameter')
			->will($this->returnCallback(
					function($name, $parameter) use(&$expectedParams){
						if($expectedParams[$name] == $parameter)
							unset($expectedParams[$name]);
					}
					));
		
		$filter->modifyQueryBuilder($qb, $fakeResolver);
		
		$this->assertEmpty($expectedParams);
	}
	
	
	/**
	 * Tests AndFilter->getType()
	 */
	public function testGetType() {
		$filter = new AndFilter(
				array($this->getMock('Slice\Search\Filter\CriteriaFilterInterface')));
		$this->assertEquals('and', $filter->getType());
	}
}

