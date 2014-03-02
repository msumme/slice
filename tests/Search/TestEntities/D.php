<?php

namespace Tests\Slice\Search\TestEntities;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @author msumme
 *
 *
 * @ORM\Entity
 */
class D {
	

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @ORM\OneToMany(targetEntity="C", mappedBy="a")
	 * @var ArrayCollection
	 */
	private $cs;
	
	/**
	 * 
	 * @var string
	 * @ORM\Column(name="d_property", type="string", length=50)
	 */
	private $dProperty;
	
	public function __construct() 
	{
		$this->cs = new ArrayCollection();
	}
	
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
/**
	 * @return the $c
	 */
	public function getCs() {
		return $this->cs->toArray();
	}

	/**
	 * @param \Tests\Slice\Search\TestEntities\C $c
	 */
	public function addC(C $c) {
		
		if(!$this->cs->contains($c)){
			$this->cs->add($c);
			$c->setD($this);
		}
		
		return $this;
	}
	
	public function removeC(C $c) 
	{
		if($this->cs->contains($c)) {
			$c->setD(null);
			$this->cs->removeElement($c);
		}
			
		return $this;
	}
	/**
	 * @return the $dProperty
	 */
	public function getDProperty() {
		return $this->dProperty;
	}

	/**
	 * @param string $dProperty
	 */
	public function setDProperty($dProperty) {
		$this->dProperty = $dProperty;
	}

	
	
	
}

?>