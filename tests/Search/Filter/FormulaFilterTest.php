<?php

namespace Tests\Slice\Search\Filter;


use Slice\Search\Filter\FormulaFilter;
/**
 * FormulaFilter test case.
 */
class FormulaFilterTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var FormulaFilter
	 */
	private $FormulaFilter;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated FormulaFilterTest::setUp()
		
		$this->FormulaFilter = new FormulaFilter('{{property.path}} < [[value]]', array('value' => 1) );
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated FormulaFilterTest::tearDown()
		$this->FormulaFilter = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Tests FormulaFilter->getType()
	 */
	public function testGetType() {
		$this->assertEquals('formula', 
		$this->FormulaFilter->getType(/* parameters */));
	}
	
	/**
	 * Tests FormulaFilter->getDQL()
	 * Tests FormulaFilter->getParameters()
	 */
	public function testGetDQL() {
		
		$fakeResolver = $this->getMock('Slice\Search\AliasResolver\AliasResolverInterface');
		
		$fakeResolver->expects($this->any())
		->method('resolveAlias')
		->with('property.path')
		->will($this->returnValue('Alias.property')); 
		
		$expected = $this->FormulaFilter->getDQL($fakeResolver);
		
		foreach($this->FormulaFilter->getParameters() as $param => $value) {
			$expected = str_replace(':'.$param, $value, $expected);
		}
		
		$this->assertEquals('(Alias.property < 1)', $expected);
	}
		
	/**
	 * Tests FormulaFilter->modifyQueryBuilder()
	 */
	public function testModifyQueryBuilder() {
		
		$fakeResolver = $this->getMock('Slice\Search\AliasResolver\AliasResolverInterface');
		
		$fakeResolver->expects($this->any())
		->method('resolveAlias')
		->with('property.path')
		->will($this->returnValue('Alias.property'));
		
		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->setMethods(array('andWhere', 'setParameter'))
			->getMock();
		
		$dql = $this->FormulaFilter->getDQL($fakeResolver);
				
		$qb->expects($this->once())
			->method('andWhere')
			->with($dql);
		
		$params = $this->FormulaFilter->getParameters();
		foreach($params as $key => $value){
		}
		
		$qb->expects($this->once())
			->method('setParameter')
			->with($key, $value);
		
		$this->FormulaFilter->modifyQueryBuilder($qb, $fakeResolver);
	}
	
	/**
	 * Tests FormulaFilter->getPropertyPaths()
	 */
	public function testGetPropertyPaths() {
		$this->assertEquals(array('property.path'), 
		$this->FormulaFilter->getPropertyPaths(/* parameters */));
	}
	
	
}

