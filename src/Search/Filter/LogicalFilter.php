<?php

namespace Slice\Search\Filter;

use Slice\Search\AliasResolver\AliasResolverInterface;

use Doctrine\ORM\QueryBuilder;

abstract class LogicalFilter implements CriteriaFilterInterface, PropertyPathFilterInterface { 
	
	protected $filters;
	
	protected $parameters;
	
	public function __construct(array $filters) 
	{
		if(empty($filters)) {
			throw new \InvalidArgumentException("\$filters must not be empty for LogicalFilter classes");
		}
		
		foreach($filters as $f) {
			if(!$f instanceof CriteriaFilterInterface)
				throw new \InvalidArgumentException('$filters must all be instanceof CriteriaFilterInterface');
			if($f instanceof AbstractDistanceFilter)
				throw new \InvalidArgumentException("Logical filters do not yet support distance filter as sub-filter");
		}
		
		$this->filters = $filters;
	}
	
	public function modifyQueryBuilder(QueryBuilder $qb, AliasResolverInterface $resolver) 
	{
		$qb->andWhere($this->getDQL($resolver));
		
		foreach($this->getParameters() as $name => $value) {
			$qb->setParameter($name, $value);
		}
	}
	
	public function getParameters() {
		if(!empty($this->parameters))
			return $this->parameters;
		
		$parameters = array();
		
		foreach($this->filters as $filter) {
			$params = $filter->getParameters();
			foreach($params as $name => $value) {
				if(array_key_exists($name, $parameters))
					throw new \RuntimeException('2 Criteria Filters have the same parameter name - will override each other\'s values:'.$name);
				
				$parameters[$name] = $value;
			}
		}
		
		return $this->parameters = $parameters;
	}
	
	public function getPropertyPaths() {
		$paths = array();
		foreach($this->filters as $filter) {
			if($filter instanceof PropertyPathFilterInterface)
				$paths = array_merge($paths, $filter->getPropertyPaths());
		}
		return array_unique($paths);
	}
	
	public function getValue()
	{
		throw new \Exception("LogicalFilter cannot getValue - it has subfilter values");
	}
	
}

?>