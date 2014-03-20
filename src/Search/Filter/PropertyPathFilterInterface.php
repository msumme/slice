<?php

namespace Slice\Search\Filter;

/**
 * This interface is necessary for FilterSet to properly construct all necessary joins 
 * and aliases when constructing queries.  
 * 
 * @author msumme
 *
 */
interface PropertyPathFilterInterface {
	
	/**
	 * Returns the property paths of the filter...
	 *  ex: for Class->property, this would be property.
	 *  ex2: for Class->property where property is association of Class2 with propertyB
	 *  	- property.propertyB
	 *
	 *
	 * @return array
	 */
	public function getPropertyPaths();
	
}

?>