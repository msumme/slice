<?php

namespace Tests\Slice\Search\Util;

use Tests\Slice\Search\BaseMockEntitiesTest;

use Slice\Search\Util\FieldUtil;


/**
 * FieldUtil test case.
 */
class FieldUtilTest extends BaseMockEntitiesTest {
	
	/**
	 *
	 * @var FieldUtil
	 */
	private $FieldUtil;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->FieldUtil = new FieldUtil($this->em);
	}
	

	/**
	 * Tests FieldUtil->getAliasName()
	 */
	public function testGetAliasName() {
		
		$expected = 'TSSTA';
		
		$alias1 = $this->FieldUtil->getAliasName('Slice:A');
		
		$alias2 = $this->FieldUtil->getAliasName('Tests\Slice\Search\TestEntities\A');
		
		$this->assertEquals($expected, $alias1);
		
		$this->assertEquals($expected, $alias2);
	}
	
	/**
	 * Tests FieldUtil->isAssociationField()
	 */
	public function testIsAssociationField() {
		
		
		
		$this->assertTrue($this->FieldUtil->isAssociationField('Slice:A', 'cs'));
		
		$this->assertFalse($this->FieldUtil->isAssociationField('Slice:A', 'aProperty'));
	}
	
	/**
	 * Tests FieldUtil->getAssociationFieldTargetClass()
	 */
	public function testGetAssociationFieldTargetClass() {
		
		$this->assertEquals(
			'Tests\Slice\Search\TestEntities\C',
			$this->FieldUtil->getAssociationFieldTargetClass('Slice:A', 'cs')
		);
		
	}
	
	/**
	 * Tests FieldUtil->getIdFieldName()
	 */
	public function testGetIdFieldName() {
		$this->assertEquals('id', $this->FieldUtil->getIdFieldName('Slice:A'));
	}
}

