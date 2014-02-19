<?php

namespace Tests\Summe\Slice\DataFixtures;

use Tests\Summe\Slice\TestEntities\C;

use Tests\Summe\Slice\TestEntities\D;

use Tests\Summe\Slice\TestEntities\B;

use Doctrine\Common\Persistence\ObjectManager;

use Tests\Summe\Slice\TestEntities\A;

use Doctrine\Common\DataFixtures\FixtureInterface;

class TestClassFixtureOne implements FixtureInterface {
	
	/* (non-PHPdoc)
	 * @see \Doctrine\Common\DataFixtures\FixtureInterface::load()
	 */
	public function load(ObjectManager $manager) {
		
		//5 a's 0->4  , aproperty = a{i}
		//2 b's, 0->1, bproperty = b{i}
		//a4->b0, a5->b1 
		
		$latLongs = array(
			array(38.937514,-92.301636), //Columbia, MO
			array(38.971688,-94.520874),	 //Kansas City
			array(39.039986,-95.652466), //Topeka, KS
			array(40.695217,-73.806152), //Brooklyn
			array(39.0911,-84.504776), //Cincinnati
		);
		
		$aGroup = array();
		
		for ($i = 0; $i < 5; $i++) {
			$a = new A();
			$a->setAProperty("a$i");
			
			$a->setLatitude($latLongs[$i][0]);
			$a->setLongitude($latLongs[$i][1]);
			
			$aGroup[] = $a;
			
			$manager->persist($a);
		}
		
		$bGroup = array();
		for ($i = 0; $i < 2; $i++) {
			$b = new B();
			$b->setBProperty("b$i");
			$bGroup[] = $b;
			$manager->persist($b);
			
			$aGroup[$i+3]->setB($b);
			
		}

		$dGroup = array();
		
		for ($i = 0; $i < 4; $i++) {
			$d = new D();
			$d->setDProperty("d$i");
			$manager->persist($d);
			$dGroup[] = $d;
		}
		
		$cGroup = array();
		
		for ($i = 0; $i < 6; $i++) {
			$c = new C();
			$c->setCProperty("c$i");
			if($i < 3) {
				$c->setA($aGroup[0]);
			}
			else {
				$c->setA($aGroup[3]);
			}
			
			if($i < 2) {
				$c->setD($dGroup[0]);
			} elseif ($i < 4) {
				$c->setD($dGroup[1]);
			} elseif ($i < 5) {
				$c->setD($dGroup[2]);
			} else {
				$c->setD($dGroup[3]);
			}
			
			$manager->persist($c);
			$manager->flush();
		}
	
		
	}

}

?>