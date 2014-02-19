<?php

namespace Summe\Slice\AliasResolver;

/**
 * This is its own interface so that Filters with complicated 
 * propertyPaths can be resolved by a parent filter so that subqueries don't conflict with their alias names. 
 * 
 * @author msumme
 *
 */
interface AliasResolverInterface {
	
	/**
	 * Returns an alias to use in the DQL query.
	 *
	 *
	 * @param string $propertyPath
	 * @return string
	 */
	public function resolveAlias($propertyPath);
	
	/**
	 * Returns an array of Alias => JoinExpression
	 *  - for a multipart propertyPath, returns multiple joins necessary
	 *    with their appropriate DQL aliases.
	 * 
	 * @param string $propertyPath
	 * @return array
	 */
	public function resolveJoins($propertyPath);
	
	/**
	 * @return string - fully qualified class name (aliases are resolved in relation to base class)
	 */
	public function getClass();
	
	public function getPrimaryIdAlias();
	
}

?>