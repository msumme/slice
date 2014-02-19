<?php

namespace Tests\Summe\Slice;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

/**
 * Assumes you are NOT using a production DB when running your tests.
 * 
 * 
 * @author msumme
 *
 */
abstract class DatabaseTestCase extends \PHPUnit_Framework_TestCase {
	//default
	private static $connectionOptions = array('driver' => 'pdo_sqlite', 'memory' => true);
	
	/**
	 *
	 * @var Client
	 */
	protected $client;
	
	/**
	 *
	 * @var EntityManager
	 */
	protected $em;
	
	public static function setConnectionOptions($options)
	{
		self::$connectionOptions = $options;
	}
	
	protected function setUp() {
		gc_collect_cycles();
		$this->createEm();
		
		parent::setUp();
	}
	
	protected function tearDown() {
		parent::tearDown();
		
	}
	
	
	
	protected function rebuildSchema() {
		
		$name = $this->em->getConnection()->getDatabase();
		
		if($name != 'testdb') {
			$this->markTestSkipped("Test Database not detected.  NOT rebuilding schema.");
			return;
		}
		
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
		
		$classes = $this->em->getMetadataFactory()->getAllMetadata();
		
		$schemaTool->dropSchema($classes);
		
		$schemaTool->createSchema($classes);
	}
		
	protected function createEm() {
		$this->em = EntityManager::create(self::$connectionOptions, Setup::createAnnotationMetadataConfiguration(array(__DIR__."/TestEntities")));
		return $this->em;
	}
	
	protected function getCallableProtectedMethod($object, $methodName) {
		$class = new \ReflectionClass($object);
		$method = $class->getMethod($methodName);
		$method->setAccessible(true);
	
		return function() use($object, $method) {
			return $method->invokeArgs($object, func_get_args());
		};
	}
	
}

?>