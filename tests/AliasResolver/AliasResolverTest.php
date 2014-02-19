<?php

namespace Tests\Summe\Slice\AliasResolver;

use Summe\Slice\Util\FieldUtil;

use Summe\Slice\AliasResolver\AliasResolver;

use Tests\Summe\Slice\BaseMockEntitiesTest;

/**
 * AliasResolver test case.
 */
class AliasResolverTest extends BaseMockEntitiesTest {
	
	/**
	 *
	 * @var AliasResolver
	 */
	private $AliasResolver;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated AliasResolverTest::setUp()
		
		$fieldUtil = new FieldUtil($this->em);
		
		$this->AliasResolver = new AliasResolver($fieldUtil, 'Tests\Summe\Slice\TestEntities\A');
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated AliasResolverTest::tearDown()
		$this->AliasResolver = null;
		
		parent::tearDown ();
	}
	
	public function __construct()
	{
		parent::__construct();
	}
 	
	/**
	 * Tests AliasResolver->getClass()
	 */
	public function testGetClass() {
		$this->assertEquals('Tests\Summe\Slice\TestEntities\A', $this->AliasResolver->getClass());
	}
	
	/**
	 * Tests AliasResolver->getPrimaryIdAlias()
	 */
	public function testGetPrimaryIdAlias() {
		$this->assertEquals('TSSTA0.id', $this->AliasResolver->getPrimaryIdAlias());
	}
	
	/**
	 * Tests AliasResolver->resolveAlias()
	 */
	public function testResolveAlias() {
		
		$this->assertEquals('TSSTA0.aProperty', $this->AliasResolver->resolveAlias('aProperty'));
		
		$this->assertEquals('cs1_d2', $this->AliasResolver->resolveAlias('cs.d'));
		$this->assertEquals('b3.bProperty', $this->AliasResolver->resolveAlias('b.bProperty'));
		$this->assertEquals('cs1_d2.dProperty', $this->AliasResolver->resolveAlias('cs.d.dProperty'));
	}
	
	/**
	 * Tests AliasResolver->resolveJoins()
	 */
	public function testResolveJoins() {
		
		
		
		
		$joinsA = $this->AliasResolver->resolveJoins('cs.d.dProperty');
		$joinsB = $this->AliasResolver->resolveJoins('cs.d');
		
		$expected = array('cs0' => 'TSSTA2.cs', 'cs0_d1' => 'cs0.d');
		
		$this->assertEquals($expected, $joinsA);
		$this->assertEquals($joinsA, $joinsB);
		
		$this->assertEquals('cs0_d1.dProperty', $this->AliasResolver->resolveAlias('cs.d.dProperty'));
		
	}
}

