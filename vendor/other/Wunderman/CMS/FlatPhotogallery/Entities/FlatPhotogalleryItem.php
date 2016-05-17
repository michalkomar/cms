<?php

	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;
	use Doctrine\ORM\Mapping\Table;
	use Gedmo\Mapping\Annotation as Gedmo;

	/**
	 * @Table(name="flat_photo_gallery_item")
	 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
	 * @ORM\HasLifecycleCallbacks
	 */
	class FlatPhotoGalleryItem extends \Kdyby\Doctrine\Entities\BaseEntity
	{

		public function __construct($gallery, $attachment, $position, $text = NULL, $title = NULL)
		{
			$this->flatPhotoGallery = $gallery;
			$this->attachment = $attachment;
			$this->position = $position;
			$this->text = $text;
			$this->title = $title;
		}

		/**
		 * @ORM\Id
		 * @ORM\Column(type="integer")
		 * @ORM\GeneratedValue
		 */
		protected $id;

		/**
		 * @Gedmo\SortableGroup
		 * @ORM\ManyToOne(targetEntity="FlatPhotoGallery")
		 * @ORM\JoinColumn(name="flat_photo_gallery_id", referencedColumnName="id")
		 */
		protected $flatPhotoGallery;

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
		protected $title;

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
		 * @param mixed $attachment
		 */
		public function setAttachment($attachment)
		{
			$this->attachment = $attachment;
			return $this;
		}

		/**
		 * @param mixed $text
		 */
		public function setText($text)
		{
			$this->text = $text;
			return $this;
		}

		/**
		 * @param mixed $title
		 */
		public function setTitle($title)
		{
			$this->title = $title;
			return $this;
		}

		/**
		 * @param mixed $position
		 */
		public function setPosition($position)
		{
			$this->position = $position;
			return $this;
		}

		/**
		 * ************************************* Getters ***************************************
		 */

		public function getId()
		{
			return $this->id;
		}
	}