<?php

namespace Slice\Search\Util;

use Doctrine\ORM\Mapping\MappingException;

use Doctrine\ORM\EntityManager;

class FieldUtil {
	
	protected static $aliases;
	
	/**
	 * 
	 * @var EntityManager
	 */
	private $em;
	
	public function __construct(EntityManager $em) 
	{
		$this->em = $em;
	}
	
	public function getAliasName($class) 
	{
		if(isset(self::$aliases[$class]))
			return self::$aliases[$class];
		
		//resolve classname
		$metadata = $this->em->getClassMetadata($class);
		$className = $metadata->getName();
		
		$parts = explode('\\', $className);
		
		$last = end($parts);
		
		array_pop($parts);
		
		$name = '';
		foreach($parts as $part) {
			$name .= substr($part, 0, 1);
		}
		$name .= $last;
		//save these so they're always consistent
		return  self::$aliases[$class] 
				= self::$aliases[$className] 
				= $name;		
	}
	
	public function isAssociationField($class, $field) 
	{
		$metadata = $this->em->getClassMetadata($class);
	
		try {
			$metadata->getAssociationMapping($field);
			return true;
		}
		catch(MappingException $e) {
			return false;
		}
	}
	
	/**
	 * 
	 * @param string $class - class owning field
	 * @param string $field - field with association
	 */
	public function getAssociationFieldTargetClass($class, $field) 
	{
		$metadata = $this->em->getClassMetadata($class);
		return $metadata->getAssociationTargetClass($field);
	}
		
	public function getIdFieldName($class) 
	{
		$metadata = $this->em->getClassMetadata($class);
		
		$columnNames = $metadata->getIdentifierFieldNames();

		if(count($columnNames) > 1)
			throw new \Exception('composite ID not yet supported');
		
		return $columnNames[0];
	}
}

?>