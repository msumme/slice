<?php

namespace Tests\Slice\Search\Result;

use Doctrine\ORM\Query;

use Slice\Search\Result\EntityResultBuilder;

/**
 * EntityResultBuilder test case.
 */
class EntityResultBuilderTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var EntityResultBuilder
	 */
	private $EntityResultBuilder;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated EntityResultBuilderTest::setUp()
		
		$this->EntityResultBuilder = new EntityResultBuilder();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated EntityResultBuilderTest::tearDown()
		$this->EntityResultBuilder = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Tests EntityResultBuilder->addSelect()
	 */
	public function testAddSelect() {
		$this->EntityResultBuilder->addSelect('a.b');
		$this->EntityResultBuilder->addSelect('a.b');
		
		$this->assertAttributeEquals(array('a.b' => true), 'selects', $this->EntityResultBuilder);
	}
	
	/**
	 * Tests EntityResultBuilder->applySelects()
	 */
	public function testApplySelects() {
		
		$this->EntityResultBuilder = $this->getMockBuilder(get_class($this->EntityResultBuilder))
			->setMethods(array('addMissingJoins', 'getJoins'))
			->getMock();
		
		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->setMethods(array('addSelect'))
			->disableOriginalConstructor()
			->getMock();
		
		$fakeResolver = $this->getMock('Slice\Search\AliasResolver\AliasResolverInterface');
		$fakeResolver->expects($this->once())
			->method('resolveAlias')
			->with('*')
			->will($this->returnValue('RootAlias'));
		
		$expectedSelects = array('RootAlias', 'a', 'b', 'c');
		
		$this->EntityResultBuilder->expects($this->once())
			->method('addMissingJoins')
			->with($qb, $fakeResolver);
		$this->EntityResultBuilder->expects($this->once())
			->method('getJoins')
			->with($fakeResolver)
			->will($this->returnValue(array('a' =>'x', 'b' => 'x','c' => 'x')));
		
		$qb->expects($this->exactly(4)) 
			->method('addSelect')
			->will($this->returnCallback(function($select) use(&$expectedSelects) {
				if(false === $key = array_search($select, $expectedSelects))
					throw new \Exception("unexpected select: $select");
				
				unset($expectedSelects[$key]);
			}));
		
		$this->EntityResultBuilder->applySelects($qb, $fakeResolver);
		
		$this->assertEmpty($expectedSelects);
	}
	
	public function buildResultDataProvider()
	{
		return array(
				[
					array(),
					array()
				],
				[
					array(
							$s = new \stdClass(),
							$s1 = new \stdClass(),
							$s2 = new \stdClass(),
					),
					array($s, $s1, $s2)
				],
				[
				array(
						array($s = new \stdClass(), 'otherProp' => 'v'),
						array($s1 = new \stdClass(), 'otherProp' => 'v'),
						array($s2 = new \stdClass(), 'otherProp' => 'v'),
				),
				array($s, $s1, $s2)
				]
				);
	}
	
	/**
	 * @dataProvider buildResultDataProvider
	 * Tests EntityResultBuilder->buildResult()
	 */
	public function testBuildResult($queryResults, $expectedResults) {
		$this->EntityResultBuilder = $this->getMockBuilder(get_class($this->EntityResultBuilder))
			->setMethods(array('applySelects'))
			->getMock();
		


		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->setMethods(array('getQuery'))
			->disableOriginalConstructor()
			->getMock();
		
		$fakeResolver = $this->getMock('Slice\Search\AliasResolver\AliasResolverInterface');
		
		$this->EntityResultBuilder->expects($this->once())
			->method('applySelects')
			->with($qb, $fakeResolver);
				
		$qb->expects($this->once())
			->method('getQuery')
			->will($this->returnValue(new MockERBQuery($queryResults, Query::HYDRATE_OBJECT)));
		
		$results = $this->EntityResultBuilder->buildResult($qb, $fakeResolver);
		
		$this->assertEquals($expectedResults, $results);
		
	}
}


class MockERBQuery {

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
