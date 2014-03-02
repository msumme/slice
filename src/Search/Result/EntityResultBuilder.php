<?php

namespace Slice\Search\Result;

use Slice\Search\Result\BaseResultBuilder;

use Doctrine\ORM\Query;

use Doctrine\ORM\QueryBuilder;

use Slice\Search\AliasResolver\AliasResolverInterface;

class EntityResultBuilder extends BaseResultBuilder implements ResultBuilderInterface {
	
	protected $selects = array();
	
	public function __construct() {
	
	}
	
	public function addSelect($propertyPath) {
		if(array_key_exists($propertyPath, $this->selects))
			return $this;
		
		$this->selects[$propertyPath] = true;
		
		return $this;
	}
	
	public function applySelects(QueryBuilder $qb, AliasResolverInterface $aliasResolver) 
	{
		$this->addMissingJoins($qb, $aliasResolver);
		
		$toBeSelected = $this->getJoins($aliasResolver);
		
		//MUST add base entity.
		$qb->addSelect($aliasResolver->resolveAlias('*'));
		
		foreach($toBeSelected as $alias => $join) {
			$qb->addSelect($alias);//this ought to work, right?
		}
	}
	
	public function buildResult(QueryBuilder $resultQb, AliasResolverInterface $aliasResolver) 
	{
		$this->applySelects($resultQb, $aliasResolver);
		
		$results = $resultQb->getQuery()->getResult(Query::HYDRATE_OBJECT);
		
		$return = array();
		//Deal with annoyance here - sort rows have to be selected and in final query in order
		//to get the order of the selected set right
		if(!empty($results)) {
			if(is_object($results[0])) {
				$return = $results;
			} elseif (is_array($results[0])) {
				foreach($results as $k => $v) {
					$return[] = $v[0];
				}
			}
		}

		return $return;
	}
	
	
	
}

?>