<?php
namespace Slice\Search\Filter;

use Slice\Search\AliasResolver\AliasResolverInterface;
use Doctrine\ORM\QueryBuilder;
/**
 * This class represents a hard use case - when you have an "IN" query that you want to be AND instead of OR
 * 
 * Most often this is seen when trying to filter by tags, as they have a many to many relationship.
 * 
 * If you use an IN(tag1,tag2) query, you do not get posts with only both tags, but rather posts with either tag.
 * 
 * If you want to find only posts with tag1, and tag2, you have to get creative.
 * 
 * This class simplifies that logic.
 * 
 * NOTE it is not compatible with any other group-by filters
 * 
 * Does not support "NOT IN" query.
 */
class AndSetFilter extends SetFilter
{
	/**
	 * @param string $propertyPath
	 * @param array $values
	 */
	public function __construct($propertyPath, array $values) 
	{
		parent::__construct($propertyPath, $values);
	}

	
	/* (non-PHPdoc)
     * @see \Slice\Search\Filter\CriteriaFilterInterface::getType()
     */
    public function getType()
    {
        return 'andset';
        
    }

    public function modifyQueryBuilder(QueryBuilder $qb, AliasResolverInterface $aliasResolver)
    {
        parent::modifyQueryBuilder($qb, $aliasResolver);
        
        $count = count($this->getValue());
        
        $qb->groupBy($aliasResolver->getPrimaryIdAlias())
            ->having("COUNT(DISTINCT ". $aliasResolver->resolveAlias($this->propertyPath) .") = $count");
    }
}

