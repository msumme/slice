<?php

namespace Slice\Search\Filter;

class RangeFilter extends AndFilter {
	
	/**
	 * 
	 * @var array
	 */
	private $value;
	
	public function __construct($propertyPath, $start= null, $end = null) {
		
		$filters = array();
		
		if($start!== null) {
			$filters[] = new GreaterThanFilter($propertyPath, $start, true);
		}
		if($end !== null) {
			$filters[] = new LessThanFilter($propertyPath, $end, true);
		}
		
		parent::__construct($filters);
		
		$this->value = array('start' => $start, 'end' => $end);
	}
	
	public function getType() {
		return 'range';
	}
	
	public function getValue() 
	{
		return $this->value;
	}
}

?>