<?php

namespace Tests\Slice\Search\Filter;

use Slice\Search\Filter\RangeFilter;

/**
 * RangeFilter test case.
 */
class RangeFilterTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var RangeFilter
	 */
	private $RangeFilter;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated RangeFilterTest::setUp()
		
		$this->RangeFilter = new RangeFilter('property', 0, 10);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated RangeFilterTest::tearDown()
		$this->RangeFilter = null;
		
		parent::tearDown ();
	}
	
	
	public function testGetValue() 
	{
		$this->assertEquals(array('start' => 0, 'end' => 10), $this->RangeFilter->getValue());
	}
	
	/**
	 * Tests RangeFilter->getType()
	 */
	public function testGetType() {
		
	    $this->assertEquals('range', $this->RangeFilter->getType());
	}
}

