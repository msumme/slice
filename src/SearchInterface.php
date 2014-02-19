<?php

namespace Summe\Slice;

use Summe\Slice\Result\ResultBuilderInterface;

use Summe\Slice\Result\ResultSet;

use Summe\Slice\Filter\FilterInterface;

/**
 * Ex: $qb = $em->createQueryBuilder()->select('classname', alias);
 * 
 * $search = new SEarch();
 * $search->setBasequeryBuilder($qb);
 * 
 * $search->addFilter(FilterInterface $filter);
 * 
 * $search->getREsults(HYDRATE_OBJECT|ARRAY|ETC);
 * 
 * $search->getTotalResultCount()
 *  
 * @author msumme
 *
 */

interface SearchInterface extends FilterableInterface {
	
	/**
	 * @return ResultSet
	 */
	public function getResultSet();
	
	/**
	 * Returns instnace of QueryBuilder with all applicable filters applied
	 *
	 * @return QueryBuilder
	 */
	public function getFilteredQueryBuilder();
	
	
	/**
	 * 
	 * @param string $hydrationMode - See Query::HYDRATE_*
	 */
	public function getResults(ResultBuilderInterface $builder = null);
	
	/**
	 * 
	 * @param int $offset
	 * @param int $length
	 * @param string $hydrationMode
	 */
	public function getResultSlice($offset, $length, ResultBuilderInterface $builder = null);
	
	
	/**
	 * @return int
	 */
	public function getTotalResultCount();

}