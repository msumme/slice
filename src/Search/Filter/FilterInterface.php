<?php

namespace Slice\Search\Filter;

use Slice\Search\AliasResolver\AliasResolverInterface;

use Doctrine\ORM\QueryBuilder;

interface FilterInterface {

	/**
	 * This makes changes to the query builder based on filter parameters.
	 * 
	 * @param QueryBuilder $qb
	 */
	public function modifyQueryBuilder(QueryBuilder $qb, AliasResolverInterface $aliasResolver);
	
}
