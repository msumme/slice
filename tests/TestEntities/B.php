<?php

namespace Tests\Summe\Slice\TestEntities;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @author msumme
 *
 *
 * @ORM\Entity
 */
class B {
	

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @ORM\Column(name="bProperty", type="string", length=50)
	 */
	private $bProperty;
	
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return the $bProperty
	 */
	public function getBProperty() {
		return $this->bProperty;
	}

	/**
	 * @param field_type $bProperty
	 */
	public function setBProperty($bProperty) {
		$this->bProperty = $bProperty;
		
		return $this;
	}

	
	
	
}

?>