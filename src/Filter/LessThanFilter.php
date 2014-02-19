<?php

namespace Summe\Slice\Filter;

use Summe\Slice\AliasResolver\AliasResolverInterface;

class LessThanFilter  extends CriteriaFilter {
	
	private $lte = false;
	
	public function __construct($propertyPath, $value, $lessThanOrEqual = false) {
		$this->lte = $lessThanOrEqual;
		parent::__construct($propertyPath, $value);
	}
	
	public function getDQL(AliasResolverInterface $resolver) {
		return $resolver->resolveAlias($this->propertyPath).' <'. ($this->lte ? '=' : '') . " :".$this->getParameterName();
	}
	
	public function getType() {
		return 'lt'.($this->lte?'e':'');
	}
	
}

