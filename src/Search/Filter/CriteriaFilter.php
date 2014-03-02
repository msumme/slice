<?php

namespace Slice\Search\Filter;

use Slice\Search\AliasResolver\AliasResolverInterface;

use Doctrine\ORM\QueryBuilder;

abstract class CriteriaFilter implements CriteriaFilterInterface, PropertyPathFilterInterface {
	
	private static $_increment = 0;
	
	protected $propertyPath;
	
	protected $value;
	
	protected $parameterName;
	
	public function __construct($propertyPath, $value) {
		$this->propertyPath = $propertyPath;
		$this->value = $value;
	}
	
	protected function getParameterName() {
		return $this->parameterName 
			?: $this->parameterName = str_replace('.', '_', $this->propertyPath)
									. '_' . self::$_increment++;
		
	}
	
	public function getPropertyPaths() {
		return array($this->propertyPath);
	}
	
	public function modifyQueryBuilder(QueryBuilder $qb, AliasResolverInterface $aliasResolver) 
	{
		$qb->andWhere($this->getDQL($aliasResolver));
		
		foreach($this->getParameters() as $name => $value) {
			$qb->setParameter($name, $value);
		}
		
	}
	
	public function getParameters() 
	{
		return array($this->getParameterName() => $this->value);
	}
	
	public function getValue() 
	{
		return $this->value;
	}
	
}

?>