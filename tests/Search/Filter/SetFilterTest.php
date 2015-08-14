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
	
	public function testGetType()
	{
	    $this->assertEquals('set', $this->SetFilter->GetType());
	}
	
	public function testGetDQL()
	{
	    $fakeResolver = $this->getMock('Slice\Search\AliasResolver\AliasResolverInterface');
	    
	    $fakeResolver->expects($this->any())
    	    ->method('resolveAlias')
    	    ->with('property')
    	    ->will($this->returnValue('Alias.property'));

	    $parameters = $this->SetFilter->getParameters();
	    $keys = array_keys($parameters);
	    
	    $this->assertEquals("Alias.property IN (:{$keys[0]})", $this->SetFilter->getDQL($fakeResolver));
	}
}

