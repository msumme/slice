<?php

namespace Summe\Slice\AliasResolver;

use Summe\Slice\Util\FieldUtil;

class AliasResolver implements AliasResolverInterface {
	
	private $_increment = 0;
	
	/**
	 * So that aliases are only resolved 1x from propertyPath
	 * @var array
	 */
	protected $aliasMap = array();

	/**
	 *
	 * @var FieldUtil
	 */
	protected $fieldUtil;
	
	/**
	 * Class name
	 * @var string
	 */
	protected $class;
	
	public function __construct(FieldUtil $fieldUtil, $class)
	{
		$this->fieldUtil = $fieldUtil;
		$this->class = $class;
	}
	
	public function getClass() 
	{
		return $this->class;
	}
	
	public function getPrimaryIdAlias() 
	{
		return $this->resolveAlias($this->fieldUtil->getIdFieldName($this->class));
	}
	
	/**
	 * Use path.* to choose all properties of joined table...
	 * 
	 * @see \Summe\Slice\SearchInterface::resolveAlias()
	 */
	public function resolveAlias($propertyPath) {
		
		if(empty($this->fieldUtil)) {
			throw new \BadMethodCallException("resolveAlias() cannot be called unless trait property \$fieldUtil has a value");
		}
		
		if (isset ( $this->aliasMap [$propertyPath] )) {
			return $this->aliasMap [$propertyPath];
		}
		
		// simple property
		if (strpos ( $propertyPath, '.' ) === false) {
			if ($propertyPath == '*') {
				return $this->aliasMap [$propertyPath] = $this->fieldUtil->getAliasName ( $this->class ) . $this->_increment ++;
			}
			
			if ($this->fieldUtil->isAssociationField ( $this->class, $propertyPath )) {
				return $this->aliasMap [$propertyPath] = $propertyPath . $this->_increment ++;
			}
			
			return $this->aliasMap [$propertyPath] = $this->resolveAlias ( '*' ) . "." . $propertyPath;
		}
		
		// multiple dots.
		$path = explode ( '.', $propertyPath );
		
		$alias = '';
		$currentClass = $this->class;
		
		$cumulativeSteps = '';
		
		$previousStep = null;
		
		foreach ( $path as $k => $step ) {
			$isAssociation = $this->fieldUtil->isAssociationField ( $currentClass, $step );
			
			$isLast = ! isset ( $path [$k + 1] );
			
			if (! $isLast && ! $isAssociation) {
				throw new \InvalidArgumentException ( "$propertyPath must be association fields until the end" );
			}
			
			if ($k === 0) {
				
				$alias = $this->resolveAlias ( $step );
				$currentClass = $this->fieldUtil->getAssociationFieldTargetClass ( $currentClass, $step );
				$previousStep = $step;
				continue;
			} elseif ($isLast) {
				
				if ($isAssociation) {
					return $this->aliasMap [$propertyPath] = $this->resolveAlias ( $previousStep ) . "_$step" . $this->_increment ++;
				}
				
				return $this->aliasMap [$propertyPath] = $this->resolveAlias ( $previousStep ) . ".$step";
			} else {
				
				$currentClass = $this->fieldUtil->getAssociationFieldTargetClass ( $currentClass, $step );
				$previousStep .= ".$step";
			}
		}
	}
	
	/**
	 * 
	 * @param unknown_type $propertyPath
	 * @throws \Exception
	 * @return array
	 */
	public function resolveJoins($propertyPath)
	{
	
		$this->resolveAlias($propertyPath);
	
		$path = explode('.', $propertyPath);
	
		$currentClass = $this->class;
		$joins = array();
		$alias = '';
		$previousAlias = $this->resolveAlias('*');
	
		$cumulativeSteps = '';
	
		foreach($path as $k => $step) {
	
			if(strpos($step, ' ') !== false)
				throw new \Exception("Invalid step value: '$step' - contains space");
				
			if(!$this->fieldUtil->isAssociationField($currentClass, $step))
				break;
				
			if($step == '*')
				break;
			//build non-conflicting aliases
			$cumulativeSteps .= ($k === 0 ? $step : ".$step");
				
			$alias = $this->resolveAlias($cumulativeSteps);
	
			$joins[$alias] = "$previousAlias.$step";
				
			//next one
			$currentClass = $this->fieldUtil->getAssociationFieldTargetClass($currentClass, $step);
				
			$previousAlias = $alias;
		}
	
		return $joins;
	}
	
}

?>