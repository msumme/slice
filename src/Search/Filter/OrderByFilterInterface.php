<?php

namespace Slice\Search\Filter;

interface OrderByFilterInterface extends FilterInterface {
	
	/**
	 * @return string ASC/DESC
	 */
	public function getDirection();
}

?>