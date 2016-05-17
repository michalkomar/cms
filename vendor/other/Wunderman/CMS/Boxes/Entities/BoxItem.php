<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Table(name="box_item")
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @ORM\HasLifecycleCallbacks
 */
class BoxItem extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @Gedmo\SortableGroup
	 * @ORM\ManyToOne(targetEntity="Box")
	 * @ORM\JoinColumn(name="box_id", referencedColumnName="id")
	 */
	protected $box;

	/**
	 * @ORM\OneToOne(targetEntity="Attachment")
	 */
	protected $attachment;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $color;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $detailColor;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $text;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $title;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $secondtitle;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $category;

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