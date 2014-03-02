<?php

namespace Slice\Search\Filter;

use Doctrine\ORM\QueryBuilder;

use Slice\Search\AliasResolver\AliasResolverInterface;

abstract class AbstractDistanceFilter implements PropertyPathFilterInterface {
	
	/**
	 * Necessary so multiple distance filters can interact without adding the same expression
	 * multiple times.
	 * @var array 
	 */
	private static $selectAdded = array();
	private static $aliases = array();
	
	
	protected $latitudePropertyPath;
	
	protected $longitudePropertyPath;
	
	protected $currentLocation;
	
	protected $expressionAlias;
	
	public function __construct($latitudePropertyPath, $longitudePropertyPath, $currentLocation, $expressionAlias) 
	{
		
		$this->latitudePropertyPath = $latitudePropertyPath;
		$this->longitudePropertyPath = $longitudePropertyPath;
		
		if(!is_array($currentLocation)
				|| !isset($currentLocation['latitude'])
				|| !isset($currentLocation['longitude'])
		) {
			throw new \Exception('$currentLocation array must include latitude and longitude keys');
		}
		$this->currentLocation = $currentLocation;
		$this->expressionAlias = $expressionAlias;
	}
	
	
	public function getDistanceCalculationExpression(AliasResolverInterface $aliasResolver) {
	
		$currentLocation = $this->currentLocation;
	
		$latAlias = $aliasResolver->resolveAlias($this->latitudePropertyPath);
		$longAlias = $aliasResolver->resolveAlias($this->longitudePropertyPath);
	
		$expression = "( 3959 * acos("
				."cos( radians({$currentLocation['latitude']}) ) * cos( radians( {$latAlias} ) )"
				." * cos( radians( {$currentLocation['longitude']} ) - radians({$longAlias}) )"
				."+ sin( radians({$currentLocation['latitude']}) ) * sin( radians( {$latAlias} ) ) ) )"; 
		
		return $expression;
	}
	
	public function getQueryAlias() {
		return $this->expressionAlias;
	}
	
	
	protected function addDistanceSelect(QueryBuilder $qb, AliasResolverInterface $aliasResolver) {
			
		$queryAlias = $this->expressionAlias;
		$expr = $this->getDistanceCalculationExpression($aliasResolver);
		
		if( isset(self::$selectAdded[$expr])
		&& in_array($qb, self::$selectAdded[$expr]) 
		&& in_array($queryAlias, self::$aliases[$expr])) {
			return;
		}
		
	
		$qb->addSelect("$expr AS $queryAlias");
	
		self::$aliases[$expr][] = $queryAlias;
		self::$selectAdded[$expr][] = $qb;
	}
	
	public function getPropertyPaths() {
		return array($this->latitudePropertyPath, $this->longitudePropertyPath);
	}
	
}

?>