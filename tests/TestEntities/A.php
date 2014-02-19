<?php

namespace Tests\Summe\Slice\TestEntities;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * 
 * @author msumme
 *
 *
 * @ORM\Entity
 */
class A {
	

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="B")
	 * @var B
	 */
	private $b;
	
	/**
	 * @ORM\OneToMany(targetEntity="C", mappedBy="a")
	 * @var ArrayCollection
	 */
	private $cs;
	
	/**
	 * 
	 * @var unknown_type
	 * @ORM\Column(name="a_property", type="string", length=50, nullable=true)
	 */
	private $aProperty;
	
	/**
     * @var float
     *
     * @ORM\Column(name="latitude", type="decimal", precision=14, scale=12, nullable=true)
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="decimal", precision=14, scale=11, nullable=true)
     */
    private $longitude;
	
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
	 * @return the $b
	 */
	public function getB() {
		return $this->b;
	}

	/**
	 * @param \Tests\Summe\Slice\TestEntities\B $b
	 */
	public function setB(B $b) {
		$this->b = $b;
		
		return $this;
	}

	/**
	 * @return the $c
	 */
	public function getCs() {
		return $this->cs->toArray();
	}

	/**
	 * @param \Tests\Summe\Slice\TestEntities\C $c
	 */
	public function addC(C $c) {
		
		if(!$this->cs->contains($c)){
			$this->cs->add($c);
			$c->setA($this);
		}
		
		return $this;
	}
	
	public function removeC(C $c) 
	{
		if($this->cs->contains($c)) {
			$c->setA(null);
			$this->cs->removeElement($c);
		}
			
		return $this;
	}
	

	/**
	 * @return the $property
	 */
	public function getAProperty() {
		return $this->aProperty;
	}

	/**
	 * @param \Tests\Summe\Slice\TestEntities\unknown_type $property
	 */
	public function setAProperty($property) {
		$this->aProperty = $property;
		
		return $this;
	}

	/**
	 * Set latitude
	 *
	 * @param string $latitude
	 * @return A
	 */
	public function setLatitude($latitude)
	{
		$this->latitude = $latitude;
	
		return $this;
	}
	
	/**
	 * Get latitude
	 *
	 * @return string
	 */
	public function getLatitude()
	{
		return $this->latitude;
	}
	
	/**
	 * Set longitude
	 *
	 * @param string $longitude
	 * @return A
	 */
	public function setLongitude($longitude)
	{
		$this->longitude = $longitude;
	
		return $this;
	}
	
	/**
	 * Get longitude
	 *
	 * @return string
	 */
	public function getLongitude()
	{
		return $this->longitude;
	}
	
	
}

?>