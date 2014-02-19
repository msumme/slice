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
class C {
	
	/**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
	private $id;
	
	/**
	 * 
	 * @var A
	 * @ORM\ManyToOne(targetEntity="A", inversedBy="cs")
	 */
	private $a;
	
	/**
	 *
	 * @var D
	 * @ORM\ManyToOne(targetEntity="D", inversedBy="cs")
	 */
	private $d;
	
	/**
	 *
	 * @var string
	 * @ORM\Column(name="c_property", type="string", length=50)
	 */
	private $cProperty;
	
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return the $a
	 */
	public function getA() {
		return $this->a;
	}

	/**
	 * @param \Tests\Summe\Slice\TestEntities\A $a
	 */
	public function setA(A $a) {
		$this->a = $a;
		$a->addC($this);
		
		return $this;
	}

	/**
	 * @return the $d
	 */
	public function getD() {
		return $this->d;
	}

	/**
	 * @param \Tests\Summe\Slice\TestEntities\D $d
	 */
	public function setD(D $d) {
		$this->d = $d;
		$d->addC($this);
		
		return $this;
	}

	/**
	 * @return the $cProperty
	 */
	public function getCProperty() {
		return $this->cProperty;
	}

	/**
	 * @param string $cProperty
	 */
	public function setCProperty($cProperty) {
		$this->cProperty = $cProperty;
	}

	
}

?>