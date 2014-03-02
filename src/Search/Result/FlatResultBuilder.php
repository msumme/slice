<?php

namespace Slice\Search\Result;

use Doctrine\ORM\Query\Expr\OrderBy;

use Slice\Search\Result\BaseResultBuilder;

use Doctrine\ORM\Query;

use Slice\Search\AliasResolver\AliasResolverInterface;

use Doctrine\ORM\QueryBuilder;

use Slice\Search\Result\ResultBuilderInterface;

class FlatResultBuilder extends BaseResultBuilder implements ResultBuilderInterface {
	
	private $_increment = 0;
	
	public function addSelect($propertyPath, $alias = null) 
	{
		if(array_key_exists($propertyPath, $this->selects))
			return $this;
		
		$this->selects[$propertyPath] = $alias;
		
		return $this;
	}
	
	public function getAliasForSelect($propertyPath)
	{
		if(!array_key_exists($propertyPath, $this->selects))
			throw new \Exception("$propertyPath was not registered");
	
		if($this->selects[$propertyPath] !== null)
			return $this->selects[$propertyPath];
		
		$parts = explode('.', $propertyPath);
		
		$calculatedAlias = end($parts);
	
		if(array_search($calculatedAlias, $this->selects) !==false)
			$calculatedAlias .= $this->_increment++;
	
		return $this->selects[$propertyPath] = $calculatedAlias;
	}
	
	public function applySelects(QueryBuilder $qb, AliasResolverInterface $aliasResolver) 
	{
		$this->addMissingJoins($qb, $aliasResolver);
		
		foreach($this->selects as $propertyPath => $alias) {
			if($propertyPath === '*') {
				$qb->addSelect($aliasResolver->resolveAlias($propertyPath));
			} elseif($alias === null) {
				//necessary to get a full result set when aliases for joins come back the same.
				$qb->addSelect($aliasResolver->resolveAlias($propertyPath)." AS ".$this->getAliasForSelect($propertyPath));
			} else {
				$qb->addSelect($aliasResolver->resolveAlias($propertyPath)." AS $alias");
			}
		}
		
		return $qb;
	}
	
	public function buildResult(QueryBuilder $resultQb, AliasResolverInterface $aliasResolver)
	{
		$this->applySelects($resultQb, $aliasResolver);
		
		$results = $resultQb->getQuery()->getResult(Query::HYDRATE_ARRAY);
		
		//Deal with the possibility that the result set is sorted by non-included value.
		$allowedKeys = array();
		foreach($this->selects as $propertyPath => $alias) {
			
			if($propertyPath == '*') {
				$fields = $resultQb->getEntityManager()->getClassMetadata($aliasResolver->getClass())->getFieldNames();
				foreach($fields as $f) {
					$allowedKeys[$f] = true;
				}
			} else {
				$allowedKeys[$this->getAliasForSelect($propertyPath)] = true;
			}
		}
		
		foreach($results as $k => $row) {
			$results[$k] = array_intersect_key($row, $allowedKeys);
		}
		
		return $results;
	}
		
}

?>