<?php

namespace Slice\Search\Filter;

use Slice\Search\AliasResolver\AliasResolverInterface;

interface CriteriaFilterInterface extends FilterInterface {
	
	/**
	 * @return string - name of type
	 */
	public function getType();
	
	/**
	 * DO NOT CALL DIRECTLY - useful for Filter that has sub-filters
	 * - such as an AND fitler and OR filter.
	 * @return string
	 */
	public function getDQL(AliasResolverInterface $resolver);
	
	/**
	 * //TODO - figure out how this ought to be - it's mainly necessary so all the filters
	 * can be used in andfilter and orfilter
	 * @return array
	*/
	public function getParameters();
	
	/**
	 * @return mixed - the value used to filter
	 */
	public function getValue();
}

?>