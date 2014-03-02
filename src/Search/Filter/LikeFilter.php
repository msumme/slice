<?php

namespace Slice\Search\Filter;

use Slice\Search\AliasResolver\AliasResolverInterface;

class LikeFilter extends CriteriaFilter
{
	private $inverse;
	
	private $wildcardCharacter;
	
	public function __construct($propertyPath, $value, $inverse = false, $wildcardCharacter = '*') {
		$this->inverse = $inverse;
		$this->wildcardCharacter = $wildcardCharacter;
		parent::__construct($propertyPath, $value);
		
		$this->translateValue();
	}
	
	public function getType() 
	{
		return ($this->inverse ? 'notlike' : 'like');
	}
	
	public function getDQL(AliasResolverInterface $resolver) {
		return $resolver->resolveAlias($this->propertyPath). ($this->inverse ? ' NOT ' :' ')."LIKE :".$this->getParameterName();
	}
	
	private function translateValue() {
		$val = $this->value;
		$c = $this->wildcardCharacter;
		
		if(strpos($val, $c) === false) {
			$val = "{$c}{$val}{$c}";
		}
		
		$this->value = str_replace($c, '%', $val);
	}
}

?>