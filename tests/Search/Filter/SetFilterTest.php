<?php
namespace Tests\Slice\Search\Filter;
use Slice\Search\Filter\SetFilter;

/**
 * SetFilter test case.
 */
class SetFilterTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var SetFilter
	 */
	private $SetFilter;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated SetFilterTest::setUp()
		
		$this->SetFilter = new SetFilter('property', array(1,23,4));
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated SetFilterTest::tearDown()
		$this->SetFilter = null;
		
		parent::tearDown ();
	}
	
	
	/**
	 * Tests SetFilter->__construct()
	 */
	public function test__construct() {
		// TODO Auto-generated SetFilterTest->test__construct()
		$this->markTestIncomplete ( "__construct test not implemented" );
		
		$this->SetFilter->__construct(/* parameters */);
	}
}

