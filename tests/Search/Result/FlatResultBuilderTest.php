<?php

namespace Tests\Slice\Search\Result;

use Doctrine\ORM\Query;

use Slice\Search\Result\FlatResultBuilder;

/**
 * FlatResultBuilder test case.
 */
class FlatResultBuilderTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var FlatResultBuilder
	 */
	private $FlatResultBuilder;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated FlatResultBuilderTest::setUp()
		
		$this->FlatResultBuilder = new FlatResultBuilder();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated FlatResultBuilderTest::tearDown()
		$this->FlatResultBuilder = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Tests FlatResultBuilder->addSelect()
	 * Tests FlatResultBuilder->getAliasForSelect()
	 */
	public function testAddSelect() {
		
		$this->FlatResultBuilder->addSelect('a', 'aAlias');
		$this->FlatResultBuilder->addSelect('b');
		$this->FlatResultBuilder->addSelect('b.bProperty');
		$this->FlatResultBuilder->addSelect('c.bProperty');
		
		$this->assertAttributeEquals(array('a' => 'aAlias', 'b' => null, 'b.bProperty' => null, 'c.bProperty' => null), 'selects', $this->FlatResultBuilder);
		
		$this->assertEquals('aAlias', $this->FlatResultBuilder->getAliasForSelect('a'));
		$this->assertEquals('b', $this->FlatResultBuilder->getAliasForSelect('b'));
		$this->assertEquals('bProperty', $this->FlatResultBuilder->getAliasForSelect('b.bProperty'));
		$this->assertEquals('bProperty0', $this->FlatResultBuilder->getAliasForSelect('c.bProperty'));
		
	}
	
	
	/**
	 * Tests FlatResultBuilder->applySelects()
	 */
	public function testApplySelects() {
		
		$this->FlatResultBuilder = $this->getMockBuilder(get_class($this->FlatResultBuilder))
			->setMethods(array('addMissingJoins'))
			->getMock();
		
		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->setMethods(array('addSelect'))
			->disableOriginalConstructor()
			->getMock();
		
		$fakeResolver = $this->getMock('Slice\Search\AliasResolver\AliasResolverInterface');
		$fakeResolver->expects($this->exactly(5))
			->method('resolveAlias')
			->will($this->returnValueMap(array(
					array('a', 'a_'),		
					array('b', 'b_'),
					array('c', 'c_'),
					array('d.Property', 'd_Property_'),
					array('e.Property', 'e_Property_'),
			)));
		
		$this->FlatResultBuilder->addSelect('a')
			->addSelect('b', 'bAlias')
			->addSelect('c')
			->addSelect('d.Property')
			->addSelect('e.Property');
				
		$this->FlatResultBuilder->expects($this->once())
			->method('addMissingJoins')
			->with($qb, $fakeResolver);
		
		$expectedSelects = array(
			'a_ AS a',
			'b_ AS bAlias',
			'c_ AS c',
			'd_Property_ AS Property',
			'e_Property_ AS Property0'		
		);
		
		$qb->expects($this->exactly(5)) 
			->method('addSelect')
			->will($this->returnCallback(function($select) use(&$expectedSelects) {
				if(false === $key = array_search($select, $expectedSelects))
					throw new \Exception("unexpected select: $select");
				
				unset($expectedSelects[$key]);
			}));
		
		$this->FlatResultBuilder->applySelects($qb, $fakeResolver);
		
		$this->assertEmpty($expectedSelects);
	}
	
	public function buildResultDataProvider() 
	{
		
		for ($i = 0; $i < 4; $i++) {
			$results["key$i"] ="value$i";
		}
		
		$expected = $results;
		unset($expected['key2']);
		
		return array(
			[
				array(
					$results,
					$results,
				),
				array(
					$expected,
					$expected		
				),
			],
			[
				array(),
				array()
			]
		);
	}
	
	/**
	 * @dataProvider buildResultDataProvider
	 * Tests FlatResultBuilder->buildResult()
	 */
	public function testBuildResult($queryResults, $expectedResults) {
		$this->FlatResultBuilder = $this->getMockBuilder(get_class($this->FlatResultBuilder))
			->setMethods(array('applySelects', 'getAliasForSelect'))
			->getMock();
		
		$this->FlatResultBuilder->addSelect('key0')
			->addSelect('key1')
			->addSelect('key3');
		
		$this->FlatResultBuilder->expects($this->exactly(3))
			->method('getAliasForSelect')
			->will($this->returnCallback(function($val) {
				return $val;
			}));
		
		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->setMethods(array('getQuery'))
			->disableOriginalConstructor()
			->getMock();
		
		$fakeResolver = $this->getMock('Slice\Search\AliasResolver\AliasResolverInterface');
		
		$this->FlatResultBuilder->expects($this->once())
			->method('applySelects')
			->with($qb, $fakeResolver);
		
		$qb->expects($this->once())
			->method('getQuery')
			->will($this->returnValue(new MockFRBQuery($queryResults, Query::HYDRATE_ARRAY)));
		
		$results = $this->FlatResultBuilder->buildResult($qb, $fakeResolver);
		
		$this->assertEquals($expectedResults, $results);
	}
}

class MockFRBQuery {

	private $results;

	private $expectedHydrate;

	public function __construct($results, $expectedhydrate)
	{
		$this->results = $results;
		$this->expectedHydrate = $expectedhydrate;
	}

	public function getResult($hydrate)
	{
		if($hydrate != $this->expectedHydrate)
			throw new \Exception("unexpected hydrate in mockquery");

		return $this->results;
	}

}