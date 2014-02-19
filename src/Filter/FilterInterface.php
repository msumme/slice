<?php

namespace Summe\Slice\Filter;


use Summe\Slice\AliasResolver\AliasResolverInterface;

use Doctrine\ORM\QueryBuilder;

interface FilterInterface {

	/**
	 * This makes changes to the query builder based on filter parameters.
	 * 
	 * @param QueryBuilder $qb
	 */
	public function modifyQueryBuilder(QueryBuilder $qb, AliasResolverInterface $aliasResolver);
	
}
