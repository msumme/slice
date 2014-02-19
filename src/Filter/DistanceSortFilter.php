<?php

namespace Summe\Slice\Filter;

use Doctrine\ORM\QueryBuilder;

use Summe\Slice\AliasResolver\AliasResolverInterface;

class DistanceSortFilter extends AbstractDistanceFilter implements OrderByFilterInterface {
	

	/**
	 * 
	 * @param string $latitudePropertyPath
	 * @param string $longitudePropertyPath
	 * @param array $currentLocation - with latitude/longitude keys
	 * @param string $expressionAlias
	 * @param string $direction
	 */
	public function __construct($latitudePropertyPath, $longitudePropertyPath, $currentLocation, $expressionAlias, $direction) 
	{
		parent::__construct($latitudePropertyPath, $longitudePropertyPath, $currentLocation, $expressionAlias);
		$this->validateAndSetDirection($direction);
	}
	
	public function modifyQueryBuilder(QueryBuilder $qb, AliasResolverInterface $aliasResolver) {
		//TODO - https://github.com/beberlei/DoctrineExtensions/tree/master/lib/DoctrineExtensions/Query/Mysql
		$queryAlias = $this->getQueryAlias();
		
		$this->addDistanceSelect($qb, $aliasResolver);
		$qb->addOrderBy($queryAlias, $this->direction);
	}
	

	/**
	 * @return string ASC/DESC
	 */
	public function getDirection() {
		return $this->direction;
	}
	
	
	protected function validateAndSetDirection($direction) {
		if( in_array(strtoupper($direction), array('ASC', 'DESC')) ) {
			$this->direction = strtoupper($direction);
		}
		elseif(is_int($direction) && $direction !== 0) {
			$this->direction = $direction > 0 ? 'ASC' : 'DESC';
		}
		else {
			throw new \InvalidArgumentException('$direction not integer or ASC/DESC');
		}
	}
	
}
