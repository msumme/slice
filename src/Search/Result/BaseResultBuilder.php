<?php

namespace Slice\Search\Result;

use Slice\Search\AliasResolver\AliasResolverInterface;

use Doctrine\ORM\QueryBuilder;

class BaseResultBuilder {
	
	protected $selects = array();
	
	public function __construct() {
	
	}
	
	protected function addMissingJoins(QueryBuilder $qb, AliasResolverInterface $aliasResolver)
	{
		$baseAlias = $aliasResolver->resolveAlias('*');
		$currentJoins = $qb->getDQLPart('join');
		if(isset($currentJoins[$baseAlias]))
			$currentJoins = $currentJoins[$baseAlias];
		else
			$currentJoins = array();
	
		$joins = $this->getJoins($aliasResolver);
	
		foreach($currentJoins as $j) {
			if(isset($joins[$j->getAlias()]))
				unset($joins[$j->getAlias()]);
		}
	
		foreach($joins as $alias => $join) {
			$qb->leftJoin($join, $alias);
		}
	
	}
	
	protected function getJoins(AliasResolverInterface $aliasResolver)
	{
		return $this->getJoinsForPaths($aliasResolver, $this->selects);
	}
	
	private function getJoinsForPaths(AliasResolverInterface $aliasResolver, array $paths) 
	{
		$joins = array();
		foreach($paths as $propertyPath => $ignore) {
			//Support virtual entity properties by ignoring the failed paths.
			try {
				$joins = array_merge($joins, $aliasResolver->resolveJoins($propertyPath));
			}
			catch(\InvalidArgumentException $e) {
				$parts = explode('.', $propertyPath);
				array_pop($parts);
				$propertyPath = implode('.', $parts);
				$joins = array_merge($joins, $this->getJoinsForPaths($aliasResolver, array($propertyPath)));
			}
				
		}
		return $joins;
	}
	
}

?>