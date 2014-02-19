<?php

namespace Tests\Summe\Slice\Result;

use Summe\Slice\Result\EntityResultBuilder;

use Summe\Slice\Util\FieldUtil;

use Summe\Slice\AliasResolver\AliasResolver;

use Tests\Summe\Slice\DataFixtures\TestClassFixtureOne;

use Summe\Slice\Filter\EqualFilter;

use Summe\Slice\Filter\FilterSet;

use Tests\Summe\Slice\BaseMockEntitiesTest;

use Summe\Slice\Result\ResultSet;

/**
 * ResultSet test case.
 */
class ResultSetTest extends BaseMockEntitiesTest {
	
	/**
	 *
	 * @var ResultSet
	 */
	private $ResultSet;
	
	private $filterSet;
	
	private $aliasResolver;
	
	private $baseQb;
	
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->rebuildSchema();
		$this->loadFixture(new TestClassFixtureOne());
		// TODO Auto-generated ResultSetTest::setUp()
		
		$this->aliasResolver = new AliasResolver(new FieldUtil($this->em), 'Tests\Summe\Slice\TestEntities\A');
		
		$this->baseQb = $this->em->createQueryBuilder();
		$this->baseQb->from('Tests\Summe\Slice\TestEntities\A', $this->aliasResolver->resolveAlias('*'));
				
		$this->filterSet = new FilterSet();
		
		
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated ResultSetTest::tearDown()
		$this->ResultSet = null;
		
		parent::tearDown ();
	}

	/**
	 * Tests ResultSet->setDefaultResultBuilder()
	 */
	public function testSetDefaultResultBuilder() {
		
		$this->ResultSet = new ResultSet($this->baseQb, $this->filterSet, $this->aliasResolver);
		$b = $this->getMock('Summe\Slice\Result\ResultBuilderInterface');
		
		$this->ResultSet->setDefaultResultBuilder($b);
		
		$this->assertAttributeEquals($b, 'resultBuilder', $this->ResultSet);
	}
	
	/**
	 * Tests ResultSet->getResults()
	 */
	public function testGetResults() {
		
		$this->ResultSet = new ResultSet($this->baseQb, $this->filterSet, $this->aliasResolver);
		
		$this->filterSet->addFilter(new EqualFilter('aProperty', 'a1'));
		
		
		$results = $this->ResultSet->getResults();
		
		$this->assertNotEmpty($results);
		
		foreach($results as $r) {
			$this->assertEquals('a1', $r->getAProperty());
		}
		
		$this->ResultSet = new ResultSet($this->baseQb, $this->filterSet, $this->aliasResolver);
		
		$this->filterSet->addFilter(new EqualFilter('aProperty', 'notAValue'));
		//Make sure no exception...
		$results = $this->ResultSet->getResults();
		$this->assertEmpty($results);
	}
	
	/**
	 * Tests ResultSet->getResultSlice()
	 */
	public function testGetResultSlice() {
		$this->ResultSet = new ResultSet($this->baseQb, $this->filterSet, $this->aliasResolver);
		
		$results = $this->ResultSet->getResultSlice(0, 3);
		$this->assertEquals(3, count($results));
		
		foreach($results as $k => $r) {
			$this->assertEquals("a$k", $r->getAProperty());
		}
		
	}
	
	/**
	 * Tests ResultSet->getTotalResultCount()
	 */
	public function testGetTotalResultCount() {
		$this->ResultSet = new ResultSet($this->baseQb, $this->filterSet, $this->aliasResolver);
		$this->filterSet->addFilter(new EqualFilter('cs.d.dProperty', 'd1'));
		
		$this->assertEquals(2, $this->ResultSet->getTotalResultCount());
	}
	
	/**
	 * Tests ResultSet->getTotalResultIds()
	 */
	public function testGetTotalResultIds() {
		
		$this->ResultSet = new ResultSet($this->baseQb, $this->filterSet, $this->aliasResolver);
		$this->filterSet->addFilter(new EqualFilter('cs.d.dProperty', 'd1'));
		
		$this->assertEquals(array('1','4'), $this->ResultSet->getTotalResultIds());
	}
	
	/**
	 * Tests ResultSet->getFilteredQueryBuilder()
	 */
	public function testGetFilteredQueryBuilder() {
		
		$this->ResultSet = new ResultSet($this->baseQb, $this->filterSet, $this->aliasResolver);
		$this->filterSet->addFilter(new EqualFilter('cs.d.dProperty', 'd1'));
		
		$qb = $this->ResultSet->getFilteredQueryBuilder();
		
		$haystack = $qb->getDQL();
		
		$needle = 'SELECT TSSTA0 FROM Tests\Summe\Slice\TestEntities\A TSSTA0 LEFT JOIN TSSTA0.cs cs1 LEFT JOIN cs1.d cs1_d2 WHERE cs1_d2.dProperty = :cs_d_dProperty_';
		
		$this->assertContains($needle, $haystack);
	}
}

