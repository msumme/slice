<?php

namespace Tests\Slice\Search\Result;

use Slice\Search\Util\FieldUtil;

use Slice\Search\AliasResolver\AliasResolver;

use Tests\Slice\Search\BaseMockEntitiesTest;

use Slice\Search\Result\BaseResultBuilder;

/**
 * BaseResultBuilder test case.
 */
class BaseResultBuilderTest extends BaseMockEntitiesTest
{
	
	/**
	 *
	 * @var BaseResultBuilder
	 */
	private $BaseResultBuilder;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated BaseResultBuilderTest::setUp()
		
		$this->BaseResultBuilder = new BaseResultBuilder();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated BaseResultBuilderTest::tearDown()
		$this->BaseResultBuilder = null;
		
		parent::tearDown ();
	}

	
	public function addMissingJoinsDataProvider()
	{
		return array(
			[
			array(
				'b.bProperty' => 'balias',
				'cs.d.dProperty' => 'dPropAlias'
			),
			array(
				'b1' => 'TSSTA0.b',
				'cs2' => 'TSSTA0.cs',
				'cs2_d3' => 'cs2.d'
			)	
			]	
		);
	}
	
	/**
	 * @dataProvider addMissingJoinsDataProvider
	 * @param unknown_type $selectedPaths
	 * @param unknown_type $expectedJoins
	 * @throws \Exception
	 */
	public function testAddMissingJoins($selectedPaths, $expectedJoins) 
	{
		$aliasResolver = $this->aliasResolver = new AliasResolver(new FieldUtil($this->em), 'Tests\Slice\Search\TestEntities\A');
		
		$selects = new \ReflectionProperty(get_class($this->BaseResultBuilder), 'selects');
		$selects->setAccessible(true);
		
		$method = $this->getCallableProtectedMethod($this->BaseResultBuilder, 'addMissingJoins');
		
		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->setMethods(array('leftJoin'))
			->disableOriginalConstructor()
			->getMock();
		
				
		$qb->expects($this->any())
			->method('leftJoin')
			->will($this->returnCallback(function($join, $alias) use(&$expectedJoins) {
				if(!isset($expectedJoins[$alias]) || $expectedJoins[$alias] != $join) {
					throw new \Exception("unexpected alias join pair - $alias -> $join");
				}
				unset($expectedJoins[$alias]);
			}));
		
		$selects->setValue($this->BaseResultBuilder, $selectedPaths);
		
		$method($qb, $aliasResolver);
		
		$this->assertEmpty($expectedJoins);
	}
	/**
	 * @dataProvider addMissingJoinsDataProvider
	 * @param unknown_type $selectedPaths
	 * @param unknown_type $expectedJoins
	 */
	public function testGetJoinsForPaths($selectedPaths, $expectedJoins) 
	{
		$aliasResolver = $this->aliasResolver = new AliasResolver(new FieldUtil($this->em), 'Tests\Slice\Search\TestEntities\A');
		//to keep the root alias set to 0 - makes it easier to duplicate test data.
		$aliasResolver->resolveAlias('*');
		
		$method = $this->getCallableProtectedMethod($this->BaseResultBuilder, 'getJoinsForPaths');
		
		$joins = $method($aliasResolver, $selectedPaths);
	
		foreach($joins as $alias => $join) {
			if(!isset($expectedJoins[$alias]) || $expectedJoins[$alias] != $join) {
				throw new \Exception("unexpected alias join pair - $alias -> $join");
			}
			unset($expectedJoins[$alias]);
		}
		
		$this->assertEmpty($expectedJoins);
	}
}

