<?php

namespace Summe\Slice;

use Summe\Slice\Filter\FilterInterface;

interface FilterableInterface  {
	
	/**
	 * Adds a filter 
	 * @param FilterInterface $filter
	 * @return FilterableInterface
	 */
	public function addFilter(FilterInterface $filter);
		
}

?>