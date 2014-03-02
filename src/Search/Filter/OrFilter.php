<?php

namespace Slice\Search\Filter;

use Slice\Search\AliasResolver\AliasResolverInterface;

class OrFilter extends LogicalFilter {
	
	public function getDQL(AliasResolverInterface $resolver) {
		
		$subCriteria = array();
		
		foreach($this->filters as $filter) {
			$subCriteria[] = $filter->getDQL($resolver);
		}
		
		return '('. join(' OR ', $subCriteria) .')';
	}
	
	public function getType() 
	{
		return 'or';
	}
	
	
}
