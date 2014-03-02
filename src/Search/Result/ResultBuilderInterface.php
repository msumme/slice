<?php

namespace Slice\Search\Result;

use Slice\Search\AliasResolver\AliasResolverInterface;

use Doctrine\ORM\QueryBuilder;

interface ResultBuilderInterface {
	
	/**
	 * 
	 * @param QueryBuilder $qb
	 * @param AliasResolverInterface $aliasResolver
	 * @return mixed - whatever is desired
	 */
	public function buildResult(QueryBuilder $qb, AliasResolverInterface $aliasResolver);
	
	/**
	 * 
	 * @param QueryBuilder $qb
	 * @param AliasResolverInterface $aliasResolver
	 * return $qb - the qb that was passed in.
	 */
	public function applySelects(QueryBuilder $qb, AliasResolverInterface $aliasResolver);
	
}

?>