<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Entity
 * @Table(name="flat_photo_gallery")
 * @ORM\HasLifecycleCallbacks
 */
class FlatPhotoGallery extends \Kdyby\Doctrine\Entities\BaseEntity
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
	 * @ORM\OneToMany(targetEntity="FlatPhotoGalleryItem", mappedBy="flatPhotoGallery")
	 * @ORM\OrderBy({"position" = "ASC"})
	 * @var \Doctrine\ORM\PersistentCollection
	 */
	protected $items;

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

		return $this->items->matching($criteria)->toArray();
	}

	public function delete()
	{
		$this->status = 'del';
	}
}