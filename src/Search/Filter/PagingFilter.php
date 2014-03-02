<?php

namespace Slice\Search\Filter;

use Slice\Search\AliasResolver\AliasResolverInterface;

use Doctrine\ORM\QueryBuilder;

class PagingFilter implements FilterInterface {
	
	private $currentPage;
	private $itemsPerPage;
	
	/**
	 * @param integer $currentPage - 0 indexed
	 * @param integer $itemsPerPage
	 */
	public function __construct($currentPage, $itemsPerPage) {
		if(!is_int($currentPage) || !is_int($itemsPerPage) 
		|| $currentPage < 0 || $itemsPerPage <= 0) {
			throw new \InvalidArgumentException('$currentPage must be zero or higher and $itemsPerPage must be a positive integer');	
		} 
		
		$this->currentPage = $currentPage;
		$this->itemsPerPage = $itemsPerPage;
	}
	
	/**
	 * @return int
	 */
	public function getCurrentPage() {
		return $this->currentPage;
	}
	
	/**
	 * @return int
	 */
	public function getItemsPerPage() {
		return $this->itemsPerPage;
	}
		
	
	public function modifyQueryBuilder(QueryBuilder $qb, AliasResolverInterface $resolver) {
	//TODO - zero result count detection somewhere? 	
		$qb->setFirstResult($this->currentPage * $this->itemsPerPage)
			->setMaxResults($this->itemsPerPage);
		
	}
}
