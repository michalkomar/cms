<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Table(name="carousel_item")
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @ORM\HasLifecycleCallbacks
 */
class CarouselItem extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @Gedmo\SortableGroup
	 * @ORM\ManyToOne(targetEntity="Carousel")
	 * @ORM\JoinColumn(name="carousel_id", referencedColumnName="id")
	 */
	protected $carousel;

	/**
	 * @ORM\OneToOne(targetEntity="Attachment")
	 */
	protected $attachment;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $text;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $text2;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $link;


	/**
	 * @ORM\Column(type="string")
	 */
	protected $linkText;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $status = 'ok';

	/**
	 * @Gedmo\SortablePosition
	 * @ORM\Column(type="integer")
	 */
	protected $position;

	/**
	 * ************************************* Setters ***************************************
	 */

	public function delete()
	{
		$this->status = 'del';
	}

	/**
	 * ************************************* Getters ***************************************
	 */

	public function getId()
	{
		return $this->id;
	}
}