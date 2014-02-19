<?php

namespace Summe\Slice\Filter;

use Doctrine\ORM\QueryBuilder;
use Summe\Slice\AliasResolver\AliasResolverInterface;

/**
 * TODO- change param names to use ':paramName' notation - handle internally
 * 
 * @author msumme
 *
 * This is like other filters, except it allows us to craft custom DQL, which can involve
 * any sort of complexity.
 * 
 * Syntax includes tokens for paths which are translated internally, and tokens for parameters.
 * 
 * The second construct argument is for the parameterValues.
 * 
 * Example formula: ({{field.path}} * 2)/3 < [[param]]
 *  This would be translated into DQL somethign like
 *  (FieldPathAlias * 2)/3 < :param_name
 *  
 *  If we passed in param => 1
 *  
 *  Then, getParameters would return array('param_name' => 1) 
 *  
 */
class FormulaFilter implements CriteriaFilterInterface, PropertyPathFilterInterface
{
	private static $_increment = 0;
	
	private $propertyPaths;
	
	private $paramTokens;
	
	private $formula;
	
	private $parameters = array();
	
	/**
	 * 
	 * @param string $formula
	 * @param array $paramValues
	 */
	public function __construct($formula, array $paramValues = [])
	{		
		$this->init($formula, $paramValues);
	}
	
	/* (non-PHPdoc)
	 * @see \Summe\Slice\Filter\CriteriaFilterInterface::getType()
	 */
	public function getType() 
	{
		return 'formula';
	}
	
	/**
	 * 
	 * @see \Summe\Slice\Filter\CriteriaFilterInterface::getDQL()
	 */
	public function getDQL(AliasResolverInterface $resolver) 
	{
		$formula = $this->formula;
		
		foreach($this->propertyPaths as $token => $path) {
			$alias = $resolver->resolveAlias($path);
			
			$formula = str_replace($token, $alias, $formula);
		}
		
		return $formula;
	}
	
	/* (non-PHPdoc)
	 * @see \Summe\Slice\Filter\CriteriaFilterInterface::getParameters()
	 */
	public function getParameters() 
	{
		return $this->parameters;
	}

	/* (non-PHPdoc)
	 * @see \Summe\Slice\Filter\FilterInterface::modifyQueryBuilder()
	 */
	public function modifyQueryBuilder(QueryBuilder $qb, AliasResolverInterface $aliasResolver) 
	{
		$qb->andWhere($this->getDQL($aliasResolver));
		
		foreach($this->getParameters() as $name => $value) {
			$qb->setParameter($name, $value);
		}
		
	}

	public function getPropertyPaths() 
	{
		return array_values($this->propertyPaths);
	}
	
	
	private function init($formula, $paramValues) 
	{
		preg_match_all('!{{(.+?)}}!', $formula, $matches);
				
		foreach($matches[1] as $k => $propertyPath) {
			$this->propertyPaths[$matches[0][$k]] = $propertyPath;
		}

		preg_match_all('!\[\[(.+?)\]\]!', $formula, $matches);

		foreach($matches[1] as $k => $param) {
			$tokenName = $param."_F".self::$_increment;
			
			$formula = str_replace($matches[0][$k], ':'.$tokenName, $formula);
			
			if(!isset($paramValues[$param]))
				throw new \InvalidArgumentException("No value found for token [[$param]] in \$paramValues");
			
			$this->parameters[$tokenName] = $paramValues[$param];
		}
						
		$this->formula = "($formula)";
	}
	
	public function getValue()
	{
		throw new \Exception("FormulaFilter cannot getValue");
	}
	
}

?>