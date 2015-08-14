<?php

namespace Tests\Slice\Search\Filter;



use Slice\Search\Filter\AndSetFilter;
/**
 * AndSetFilter test case.
 */
class AndSetFilterTest extends \PHPUnit_Framework_TestCase
{


    /**
     * Tests AndSetFilter->getType()
     */
    public function testGetType()
    {
        $filter = new AndSetFilter('property', array(1,2,3));
        $this->assertEquals('andset', $filter->getType());
    }

    /**
     * Tests AndSetFilter->modifyQueryBuilder()
     */
    public function testModifyQueryBuilder()
    {
        
        $fakeResolver = $this->getMock('Slice\Search\AliasResolver\AliasResolverInterface');
        
        $fakeResolver->expects($this->any())
            ->method('resolveAlias')
            ->with('property')
            ->will($this->returnValue('Alias.property'));
        
        $fakeResolver->expects($this->once())
            ->method('getPrimaryIdAlias')
            ->will($this->returnValue('PRIMARY_ALIAS'));
        
        $filter = new AndSetFilter('property', array(1,2,3));
        
        $parameters = $filter->getParameters();
        $keys = array_keys($parameters);
         
        $expectedDQL = "Alias.property IN (:{$keys[0]})";
        
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        
        $qb->expects($this->once())
            ->method('andWhere')
            ->with($expectedDQL);
        
        $qb->expects($this->once())
            ->method('setParameter')
            ->with($keys[0], array(1,2,3));
        
        $qb->expects($this->once())
            ->method('groupBy')
            ->with('PRIMARY_ALIAS')
            ->will($this->returnValue($qb));
        
        $qb->expects($this->once())
            ->method('having')
            ->with("COUNT(DISTINCT Alias.property) = 3");
        
        
        $filter->modifyQueryBuilder($qb, $fakeResolver);
    }
}

