<?php

namespace Slice\Search\Filter;


use Slice\Search\AliasResolver\AliasResolverInterface;

use Doctrine\ORM\QueryBuilder;

class NullFilter extends CriteriaFilter 
{
	private $inverse;
	
	public function __construct($propertyPath, $inverse = false) {
		$this->inverse = (bool) $inverse;
		parent::__construct($propertyPath, null);
	}
		
	public function getDQL(AliasResolverInterface $resolver) 
	{
		return $resolver->resolveAlias($this->propertyPath)." IS"
				. ($this->inverse ? ' NOT' : '')  
				. " NULL";
		
	}
	
	public function getParameters() {
		return array();
	}
	
	public function getType() 
	{
		return 'null';
	}
}

?>