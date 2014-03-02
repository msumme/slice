<?php

namespace Slice\Search\Filter;

use Slice\Search\AliasResolver\AliasResolverInterface;

use Doctrine\ORM\QueryBuilder;

class OrderByFilter implements OrderByFilterInterface, PropertyPathFilterInterface {
	
	protected $propertyPath;
	
	protected $direction;
	
	/**
	 * 
	 * @param unknown_type 
	 * @param mixed string/int $direction 'ASC'/positive for ascending, 'DESC'/negative for descending
	 */
	public function __construct($propertyPath, $direction) {
		$this->propertyPath = $propertyPath;
		$this->validateAndSetDirection($direction);		
	}
	
	public function modifyQueryBuilder(QueryBuilder $qb, AliasResolverInterface $resolver) {
		$qb->addOrderBy($resolver->resolveAlias($this->propertyPath), $this->direction);
	}
	
	/**
	 * @return string - aliased property
	 */
	public function getPropertyPaths() {
		return array($this->propertyPath);
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