<?php

namespace Summe\Slice\Filter;


use Summe\Slice\AliasResolver\AliasResolverInterface;

class AndFilter extends LogicalFilter {
	
	public function getDQL(AliasResolverInterface $resolver) {
		
		$subCriteria = array();
		
		foreach($this->filters as $filter) {
			$subCriteria[] = $filter->getDQL($resolver);
		}
		
		return '('. join(' AND ', $subCriteria) .')';
	}
	
	public function getType() 
	{
		return 'and';
	}
	
	
}

?>