<?php

namespace Summe\Slice\Filter;

use Summe\Slice\AliasResolver\AliasResolverInterface;

use Doctrine\ORM\QueryBuilder;

class DistanceCriteriaFilter extends AbstractDistanceFilter implements CriteriaFilterInterface {

	protected $distanceLimit;
	
	/**
	 * 
	 * @param string $latitudePropertyPath
	 * @param string $longitudePropertyPath
	 * @param array $currentLocation latitude/longitude keys 
	 * @param numeric $distanceLimit
	 * @param string $expressionAlias
	 */
	public function __construct($latitudePropertyPath, $longitudePropertyPath, $currentLocation, $distanceLimit, $expressionAlias) {
		
		$this->distanceLimit = (int)$distanceLimit;
		parent::__construct($latitudePropertyPath, $longitudePropertyPath, $currentLocation, $expressionAlias);
		
	}
	
	
	/**
	 * 
	 * @see \Summe\Slice\Filter\CriteriaFilter::modifyQueryBuilder()
	 */
	public function modifyQueryBuilder(QueryBuilder $qb, AliasResolverInterface $aliasResolver) {
		//TODO - https://github.com/beberlei/DoctrineExtensions/tree/master/lib/DoctrineExtensions/Query/Mysql
	
		$this->addDistanceSelect($qb, $aliasResolver);
		
		$queryAlias = $this->getQueryAlias();
		
		$qb->andWhere($this->getDQL($aliasResolver))
			->andHaving("{$queryAlias} < {$this->distanceLimit} OR {$queryAlias} IS NULL");
	}
	
	public function getParameters() {
		return array();
	}
	
	public function getType() {
		return 'distance';
	}
	
	public function getValue() 
	{
		return $this->distanceLimit;
	}
	
	/**
	 * NOTE - this should not be called by anything outside this class
	 * @see \Summe\Slice\Filter\CriteriaFilterInterface::getDQL()
	 */
	public function getDQL(AliasResolverInterface $aliasResolver) {
		$currentLocation = $this->currentLocation;
		
		$latAlias = $aliasResolver->resolveAlias($this->latitudePropertyPath);
		$longAlias = $aliasResolver->resolveAlias($this->longitudePropertyPath);
		
		$queryAlias = $this->getQueryAlias();
		
		$roughLatitudeLimit = ($this->distanceLimit ) /  65;
		$upperLat = $currentLocation['latitude'] + $roughLatitudeLimit;
		$lowerLat = $currentLocation['latitude'] - $roughLatitudeLimit;
		
		$roughLongitudeLimit = ($this->distanceLimit) / 45;
		$upperLong = $currentLocation['longitude'] + $roughLongitudeLimit;
		$lowerLong = $currentLocation['longitude'] - $roughLongitudeLimit;
		
		$dql = "(($latAlias BETWEEN $lowerLat AND $upperLat)"
				." AND ($longAlias BETWEEN $lowerLong AND $upperLong))";
		
		return $dql;
	}
}
