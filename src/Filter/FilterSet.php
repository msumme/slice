<?php

namespace Summe\Slice\Filter;

use Doctrine\ORM\QueryBuilder;

use Summe\Slice\AliasResolver\AliasResolverInterface;

class FilterSet {
	
	protected $orderFilters = array();
	
	protected $pagingFilter;
	
	protected $criteriaFilters = array();
	
	public function __construct() 
	{
	}
	
	public function addFilter(FilterInterface $filter) 
	{
		if ($filter instanceof CriteriaFilterInterface) {
			$this->addCriteriaFilter($filter);
		} elseif ($filter instanceof OrderByFilterInterface) {
			$this->addOrderFilter($filter);
		} elseif ($filter instanceof PagingFilter) {
			$this->setPagingFilter($filter);
		} else {
			throw new \Exception('unsupported filter type:'.get_class($filter));
		}
		
		return $this;
	}
	
	/**
	 * Applies criteria filters and necessary joins.
	 * @param QueryBuilder $qb
	 * @param AliasResolverInterface $aliasResolver
	 * @return QueryBuilder
	 */
	public function filterForCount(QueryBuilder $qb, AliasResolverInterface $aliasResolver) 
	{
		$normalFilters = array();
		$distanceFilters = array();
		foreach($this->criteriaFilters as $filter) {
			if($filter instanceof DistanceCriteriaFilter)
				$distanceFilters[] = $filter;
			else
				$normalFilters[] = $filter;
		}
		
		$propertyPaths = $this->getPropertyPathsFromFilters($distanceFilters);
		$this->applyMissingPropertyJoins($qb, $aliasResolver, $propertyPaths);
		
		foreach($distanceFilters as $filter) {
			$expr = $filter->getDistanceCalculationExpression($aliasResolver);
			$alias = $filter->getQueryAlias();
			$dql = $filter->getDQL($aliasResolver);
			$distanceLimit = $filter->getValue();

			$qb->andWhere($dql)
				->andWhere("$expr < $distanceLimit");
		}
		
		
		
		return $this->applyFiltersAndJoins($normalFilters, $qb, $aliasResolver);
	}
	
	/**
	 * Applies criteria filter and necessary joins.
	 * Assumes WHERE will be set of ids - because paging requirements for one-to-many joins 
	 * @param QueryBuilder $qb
	 * @param AliasResolverInterface $aliasResolver
	 */
	public function filterForGetResults(QueryBuilder $qb, AliasResolverInterface $aliasResolver)
	{
		$normalFilters = array();
		foreach($this->criteriaFilters as $filter) {
			if(!$filter instanceof DistanceCriteriaFilter)
				$normalFilters[] = $filter;
		}
		
		$this->applyFiltersAndJoins($normalFilters, $qb, $aliasResolver);
		
		$this->applyFiltersAndJoins($this->orderFilters, $qb, $aliasResolver);
		
		return $qb;
	}
	
	public function filterForSelectRows(QueryBuilder $qb, AliasResolverInterface $aliasResolver) 
	{
		
		$this->applyFiltersAndJoins($this->criteriaFilters, $qb, $aliasResolver);
		
		$this->applyFiltersAndJoins($this->orderFilters, $qb, $aliasResolver);
				
		if(isset($this->pagingFilter))
			$this->pagingFilter->modifyQueryBuilder($qb, $aliasResolver);
		
		return $qb;
	}

	
	protected function applyFiltersAndJoins($filters, QueryBuilder $qb, AliasResolverInterface $aliasResolver) 
	{
		$propertyPaths = $this->getPropertyPathsFromFilters($filters);
		
		$orderFilters = array();
				
		foreach($filters as $filter) {
			if($filter instanceof OrderByFilter) 
				$orderFilters[] = $filter;	
			
			$filter->modifyQueryBuilder($qb, $aliasResolver);
		}
		
		//add order selects 
		foreach($this->getPropertyPathsFromFilters($orderFilters) as $path) {
			$qb->addSelect($aliasResolver->resolveAlias($path));
		}
				
		$this->applyMissingPropertyJoins($qb, $aliasResolver, $propertyPaths);
		
		return $qb;
	}
	
	
	protected function getPropertyPathsFromFilters(array $filters) 
	{
		$propertyPaths = array();
		foreach($filters as $filter) {
			if($filter instanceof PropertyPathFilterInterface)
				$propertyPaths = array_merge($propertyPaths, $filter->getPropertyPaths());
		}
		return array_unique($propertyPaths);
	}
	
	protected function applyMissingPropertyJoins(QueryBuilder $qb, AliasResolverInterface $aliasResolver, array $propertyPaths) {

		$joins = array();
		foreach($propertyPaths as $p) {
			$joins = array_merge($joins, $aliasResolver->resolveJoins($p));
		}
		
		$baseAlias = $aliasResolver->resolveAlias('*');
		$currentJoins = $qb->getDQLPart('join');
		
		if(isset($currentJoins[$baseAlias]))
			$currentJoins = $currentJoins[$baseAlias];
		else
			$currentJoins = array(); 
		
		foreach($currentJoins as $j) {
			if(isset($joins[$j->getAlias()])) 
				unset($joins[$j->getAlias()]);
		}
		
		foreach($joins as $alias => $join) {
			$qb->leftJoin($join, $alias);
		}
	}
	
	private function addCriteriaFilter($filter)
	{
		if(!in_array($filter, $this->criteriaFilters))
			$this->criteriaFilters[] = $filter;
	}
	
	private function addOrderFilter($filter)
	{
		if(!in_array($filter, $this->orderFilters))
			$this->orderFilters[] = $filter;
	}
	
	private function setPagingFilter($filter)
	{
		$this->pagingFilter = $filter;
	}
	
}

?>