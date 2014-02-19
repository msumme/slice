<?php

namespace Tests\Summe\Slice\Filter;

use Summe\Slice\Filter\LessThanFilter;

use Summe\Slice\Filter\GreaterThanFilter;

use Summe\Slice\Filter\OrFilter;

/**
 * OrFilter test case.
 */
class OrFilterTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testArgumentValidation() 
	{
		$filter = new OrFilter(array('not_class'));
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testEmpty() {
		$filter = new OrFilter(array());
	}
	
	
	public function testCombinations() 
	{
		
		$fakeResolver = $this->getMock('Summe\Slice\AliasResolver\AliasResolverInterface');
		
		$fakeResolver->expects($this->any())
		->method('resolveAlias')
		->with('property')
		->will($this->returnValue('Alias.property'));
		
		$filters = array();
		
		$filters[] = new GreaterThanFilter('property', 1);
		$filters[] = new LessThanFilter('property', 2);
		
		$filter = new OrFilter($filters);
		
		$parts = array();
		$expectedParams = array();
		foreach($filters as $f) {
			$parts[] = $f->getDQL($fakeResolver);
			$expectedParams = array_merge($expectedParams, $f->getParameters());
		}
		
		$expected = "(". join(' OR ', $parts) .")";
		
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
	 * Tests OrFilter->getType()
	 */
	public function testGetType() {
		$filter = new OrFilter(
				array($this->getMock('Summe\Slice\Filter\CriteriaFilterInterface')));
		$this->assertEquals('or', $filter->getType());
	}
}

