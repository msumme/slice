<?php

namespace Summe\Slice\Filter;

use Summe\Slice\AliasResolver\AliasResolverInterface;

class SetFilter extends CriteriaFilter {
	
	private $inverse;
	
	public function __construct($propertyPath, array $values, $inverse = false) 
	{
		parent::__construct($propertyPath, $values);
		$this->inverse = $inverse;
	}
	
	public function getType() 
	{
		return 'set';
	}
	
	public function getDQL(AliasResolverInterface $aliasResolver) 
	{
		return $aliasResolver->resolveAlias($this->propertyPath) . ($this->inverse ? ' NOT' : '' ) ." IN (:".$this->getParameterName().")";
	}
	
}

?>