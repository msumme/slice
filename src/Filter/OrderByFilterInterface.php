<?php

namespace Summe\Slice\Filter;

interface OrderByFilterInterface extends FilterInterface {
	
	/**
	 * @return string ASC/DESC
	 */
	public function getDirection();
}

?>