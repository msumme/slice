<?php

namespace Slice\Search;

use Slice\Search\Filter\FilterInterface;

interface FilterableInterface  {
	
	/**
	 * Adds a filter 
	 * @param FilterInterface $filter
	 * @return FilterableInterface
	 */
	public function addFilter(FilterInterface $filter);
		
}

?>