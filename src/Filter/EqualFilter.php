<?php

namespace Summe\Slice\Filter;

use Summe\Slice\AliasResolver\AliasResolverInterface;

class EqualFilter extends CriteriaFilter {
	
	private $inverse;
	public function __construct($propertyPath, $value, $inverse = false) {
		$this->inverse = (bool) $inverse;
		parent::__construct( $propertyPath, $value );
	}
	
	public function getType() {
		return 'equal';
	}
		
	/*
	 * (non-PHPdoc) @see \Summe\Slice\Filter\FilterInterface::getDQL()
	 */
	public function getDQL(AliasResolverInterface $aliasResolver) {
		return $aliasResolver->resolveAlias($this->propertyPath) . ($this->inverse ? ' !' : ' ' ) ."= :".$this->getParameterName();
	}
	
	
}

?>