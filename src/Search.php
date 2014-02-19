<?php

namespace Summe\Slice;

use Summe\Slice\Result\EntityResultBuilder;

use Summe\Slice\Filter\FilterInterface;

use Summe\Slice\Result\FlatResultBuilder;

use Summe\Slice\Result\ResultBuilderInterface;

use Summe\Slice\Result\ResultSet;

use Summe\Slice\Filter\FilterSet;

use Summe\Slice\AliasResolver\AliasResolverInterface;

use Summe\Slice\AliasResolver\AliasResolver;

use Summe\Slice\Filter\PropertyPathFilterInterface;

use Summe\Slice\Util\FieldUtil;

use Doctrine\ORM\EntityManager;

use Doctrine\ORM\Query;

use Doctrine\ORM\QueryBuilder;

/**
 * Returns a data set based on applying FilterInterface objects to a querybuilder 
 * 	(internally - several querybuilders so that maxResults behaves as expected)
 * 
 * TODO - figure out how to unit test this.
 * 
 * @author msumme
 *
 */
class Search implements SearchInterface {
		
	/**
	 * 
	 * @var EntityManager
	 */
	protected $em;
	
	/**
	 * 
	 * @var string
	 */
	protected $class;

	/**
	 * 
	 * @var AliasResolver
	 */
	protected $aliasResolver;

	/**
	 * 
	 * @var FilterSet
	 */	
	protected $filterSet;
	
	/**
	 * Keep things in sync.
	 * @var boolean
	 */
	protected $_locked = false;

	/**
	 * 
	 * @var ResultSet
	 */
	protected $_lockedResultSet;
		
	/**
	 * Only for SELECT queries - not SET queries, or delete
	 * @param QueryBuilder $queryBuilder
	 * @param unknown_type $primaryAliasedId - ex. Listing.id - id of primary pageable entity (for maxResults to work properly)
	 * @param AliasResolverInterface $aliasResolver
	 */
	function __construct(EntityManager $em, $class) 
	{
		$this->em = $em;
		//resolve primary classname.		
		$meta = $this->em->getClassMetadata($class);
		$this->class = $meta->getName();
		$this->aliasResolver = new AliasResolver(new FieldUtil($this->em), $this->class);
		
		$this->filterSet = new FilterSet();
		
	}
	
	public function resolveAlias($propertyPath) 
	{
		return $this->aliasResolver->resolveAlias($propertyPath);
	}

	public function addFilter(FilterInterface $filter)
	{
		if($this->_locked)
			throw new \BadMethodCallException('cannot call addFilter after getting results');
		
		$this->filterSet->addFilter($filter);
		
		return $this;
	}
	
	/**
	 * DOES NOT LOCK FILTERS  
	 * @see \Summe\Slice\SearchInterface::getResultSet()
	 */
	public function getResultSet() 
	{
		//return result set with cloned filter set (so that it won't change)
		return  new ResultSet($this->getBaseQb(), clone $this->filterSet, $this->aliasResolver);
	}	
	

	/**
	 *
	 * @see \Summe\Slice\SearchInterface::getQueryBuilder()
	 * @return QueryBuilder
	 */
	public function getFilteredQueryBuilder()
	{
		$this->lock();
	
		$qb = $this->_lockedResultSet->getFilteredQueryBuilder();
	
		return $qb;
	}
	
	public function getResults(ResultBuilderInterface $builder = null) 
	{
		$this->lock();
		
		return $this->_lockedResultSet->getResults($builder);
	}
	
	
	public function getResultSlice($offset, $length, ResultBuilderInterface $builder = null) 
	{
		$this->lock();
		
		return $this->_lockedResultSet->getResultSlice($offset, $length, $builder);
	}
	
	/**
	 * 
	 * @see \Summe\Slice\SearchInterface::getTotalResultCount()
	 * @return int - count irrespective of maxResults or firstResult
	 */
	public function getTotalResultCount() 
	{
		$this->lock();

		return $this->_lockedResultSet->getTotalResultCount();
	}
	
	public function getTotalResultIds() 
	{
		$this->lock();
		
		return $this->_lockedResultSet->getTotalResultIds();
	}
	
	
	
	public function __clone()
	{
		$this->_locked = false;
		$this->_lockedResultSet = null;
	}
	
	protected function lock()
	{
		if($this->_locked)
			return;

		$this->_lockedResultSet = new ResultSet($this->getBaseQb(), $this->filterSet, $this->aliasResolver);
		
		$this->_locked = true;
	}
	
	/**
	 * @return QueryBuilder
	 */
	private function getBaseQb() {
		$qb = $this->em->createQueryBuilder();
		$qb->from($this->class, $this->resolveAlias("*"));
		return $qb;
	}
	

	
}

?>