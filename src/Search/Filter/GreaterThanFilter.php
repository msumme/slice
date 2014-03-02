<?php

namespace Slice\Search\Filter;

use Slice\Search\AliasResolver\AliasResolverInterface;

class GreaterThanFilter extends CriteriaFilter {
	
	private $gte = false;
	
	public function __construct($propertyPath, $value, $greaterThanOrEqual = false) {
		$this->gte = $greaterThanOrEqual;
		parent::__construct($propertyPath, $value);
	}
	
	public function getDQL(AliasResolverInterface $resolver) {
		return $resolver->resolveAlias($this->propertyPath).' >'. ($this->gte ? '=' : '') . " :".$this->getParameterName();
	}
	
	public function getType() {
		return 'gt'.($this->gte?'e':'');
	}
}

?>