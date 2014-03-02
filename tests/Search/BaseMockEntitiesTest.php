<?php

namespace Tests\Slice\Search;

use Tests\Slice\Search\DatabaseTestCase;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

abstract class BaseMockEntitiesTest extends DatabaseTestCase {
	
	protected $classes = [
		'Tests\Slice\Search\TestEntities\A',
		'Tests\Slice\Search\TestEntities\B',
		'Tests\Slice\Search\TestEntities\C',
		'Tests\Slice\Search\TestEntities\D',
	];

	private function setMetadataDriver() 
	{
		$reader = new AnnotationReader();
		
		$metadataDriver = new AnnotationDriver($reader, array(__DIR__."/TestEntities/"));
		
		$config = $this->em->getConfiguration();
		$config->setMetadataDriverImpl($metadataDriver);
		
		$config->setEntityNamespaces(array('Slice' => 'Tests\Slice\Search\TestEntities'));
	}
	
	protected function loadFixture($fixture) {
		$loader = new \Doctrine\Common\DataFixtures\Loader;
		$loader->addFixture($fixture);
		$purger = new \Doctrine\Common\DataFixtures\Purger\ORMPurger($this->em);
		$executor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor($this->em, $purger);
		$executor->execute($loader->getFixtures());
	}
	
	protected function rebuildSchema() {
		//$this->setMetadataDriver();
		parent::rebuildSchema($this->classes);
	}
	
	protected function setUp() {
		parent::setUp();
		$this->setMetadataDriver();
	}
	
	protected function tearDown()
	{
		parent::tearDown();
		gc_collect_cycles();
	}
	
}

?>