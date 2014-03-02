<?php

namespace Slice\Search\Result;

use Slice\Search\AliasResolver\AliasResolverInterface;

use Slice\Search\Filter\FilterSet;

use Doctrine\ORM\QueryBuilder;

class ResultSet {
	
	private $_cache = array();
		
	private $baseQb;
	
	private $filterSet;
	
	private $aliasResolver;
	
	private $class;
	
	private $useResultCache = true;
	
	private $resultCacheLifetime = 14400;
	
	/**
	 * 
	 * @var ResultBuilderInterface
	 */
	private $resultBuilder;
	
	public function __construct(
			QueryBuilder $qb, 
			FilterSet $filterSet, 
			AliasResolverInterface $aliasResolver
	) {
		$this->baseQb = $qb;
		$this->filterSet = $filterSet;
		$this->aliasResolver = $aliasResolver;
	}
	
	public function setUseResultCache($useCache, $lifetime = 14400) {
		$this->useResultCache = (bool) $useCache;
		$this->resultCacheLifetime = (int)$lifetime;
	}
	
	public function setDefaultResultBuilder(ResultBuilderInterface $builder) {
		$this->resultBuilder = $builder;
		
		return $this;
	}
	
	public function getResults(ResultBuilderInterface $builder = null) 
	{
		$qb = clone $this->baseQb;
		$this->filterSet->filterForGetResults($qb, $this->aliasResolver);
		
		$idAlias = $this->aliasResolver->getPrimaryIdAlias();
		$qb->andWhere("{$idAlias} IN (:selected_ids_for_search)");
		
		$ids = $this->getSelectedIds();
		$qb->setParameter('selected_ids_for_search', $ids);
		
		if(null === $builder)
			$builder = $this->getDefaultResultBuilder();
		
		return $builder->buildResult($qb, $this->aliasResolver);
	}
	
	public function getResultSlice($offset, $length, ResultBuilderInterface $builder = null) 
	{
		$qb = clone $this->baseQb;
		$this->filterSet->filterForGetResults($qb, $this->aliasResolver);
		
		$idAlias = $this->aliasResolver->getPrimaryIdAlias();
		$qb->andWhere("{$idAlias} IN (:selected_ids_for_search)");
		
		$ids = array_slice($this->getSelectedIds(), $offset, $length);
		$qb->setParameter('selected_ids_for_search', $ids);
		
		if(null === $builder)
			$builder = $this->getDefaultResultBuilder();
		
		return $builder->buildResult($qb, $this->aliasResolver);
	}	
	
	public function getTotalResultCount() 
	{
		if(isset($this->_cache['count']))
			return $this->_cache['count'];
		
		$idAlias = $this->aliasResolver->getPrimaryIdAlias();
		
		$qb = clone $this->baseQb;
		
		//This would add distance select - but we dont want that, so we add this here first.
		$this->filterSet->filterForCount($qb, $this->aliasResolver);
		
		$qb->select("COUNT(DISTINCT {$idAlias}) as search_count");
		
		$query = $qb->getQuery();
		
		if($this->useResultCache) {
			$query->useResultCache(true, $this->resultCacheLifetime);
		}
		
		return $this->_cache['count'] = $query->getSingleScalarResult();
	}
	
	public function getTotalResultIds() 
	{
		if(isset($this->_cache['totalIds']))
			return $this->_cache['totalIds'];
		//get selected ids, but ignore paging.		
		return $this->_cache['totalIds'] = $this->getIds(true);
	}
	
	
	
	/**
	 * Returns query builder with no selects, but with criteria, order, paging, joins
	 * 
	 * @return QueryBuilder
	 */
	public function getFilteredQueryBuilder(ResultBuilderInterface $builder = null) 
	{
		$qb = clone $this->baseQb;
		
		$this->filterSet->filterForSelectRows($qb, $this->aliasResolver);
		
		if($builder === null)
			$builder = $this->getDefaultResultBuilder();
		
		$builder->applySelects($qb, $this->aliasResolver);
		
		return $qb;
	}
	
	private function getSelectedIds() 
	{
		if(isset($this->_cache['selectedIds']))
			return $this->_cache['selectedIds'];
		
		return $this->_cache['selectedIds'] = $this->getIds();
	}
	
	private function getIds($ignorePaging = false) 
	{
		$qb = $this->getIdSelectQb();
		
		if($ignorePaging) {
			$qb->setMaxResults(null);
			$qb->setFirstResult(null);
		}
		
		$query = $qb->getQuery();
		
		if($this->useResultCache) {
			$query->useResultCache(true, $this->resultCacheLifetime);
		}
		
		$result = $query->getScalarResult();
		
		$ids = array();
		
		foreach($result as $r) {
			$ids[] = $r['PRIMARY_ID'];
		}
		return $ids;
	}
	
	/**
	 * 
	 * @return QueryBuilder
	 */
	private function getIdSelectQb() 
	{
		$qb = clone $this->baseQb;
		
		$idAlias = $this->aliasResolver->getPrimaryIdAlias();
		$qb->select("$idAlias AS PRIMARY_ID");
		
		$this->filterSet->filterForSelectRows($qb, $this->aliasResolver);
		
		return $qb;
	}
	
	private function getDefaultResultBuilder() 
	{
		if($this->resultBuilder)
			return $this->resultBuilder;
		
		$builder = new EntityResultBuilder();
		$builder->addSelect('*');
		return $this->resultBuilder = $builder;
	}
	
}

?>