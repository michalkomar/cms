<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Entity
 * @Table(name="carousel")
 * @ORM\HasLifecycleCallbacks
 */
class Carousel extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $name;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $status = 'ok';

	/**
	 * @ORM\OneToMany(targetEntity="CarouselItem", mappedBy="carousel")
	 * @ORM\OrderBy({"position" = "ASC"})
	 */
	protected $items;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $showNavigation = 1;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $showHeader = 1;

	/**
	 * ************************************* Setters ***************************************
	 */


	/**
	 * ************************************* Getters ***************************************
	 */

	public function getId()
	{
		return $this->id;
	}

	public function getItems($status = 'ok')
	{
		$criteria = Criteria::create();
		$criteria->where(Criteria::expr()->eq('status', $status))->orderBy(array('position' => 'ASC'));

		return $this->items->matching($criteria);
	}

	public function destroy()
	{
		$this->status = 'del';
	}
}