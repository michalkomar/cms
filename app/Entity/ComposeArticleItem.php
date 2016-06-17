<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Petr Besir Horáček <sirbesir@gmail.com>
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @ORM\Table(name="compose_article_item")
 */
class ComposeArticleItem extends \Kdyby\Doctrine\Entities\BaseEntity
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
	protected $type;

	/**
	 * @Gedmo\SortableGroup
	 * @ORM\OneToOne(targetEntity="ComposeArticle")
	 */
	protected $composeArticle;

	/**
	 * @ORM\OneToMany(targetEntity="ComposeArticleItemParam", mappedBy="composeArticleItem")
	 */
	protected $params;

	/**
	 * @Gedmo\SortablePosition
	 * @ORM\Column(type="integer")
	 */
	protected $position;

	/**
	 * @Gedmo\SortableGroup
	 * @ORM\Column(type="string")
	 */
	protected $status = 'ok';

	public function remove()
	{
		$this->status = 'del';
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return ComposeArticle
	 */
	public function getComposeArticle()
	{
		return $this->composeArticle;
	}

	/**
	 * @return ComposeArticleItemParam[]|ArrayCollection
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @return mixed
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return $this->status;
	}
}
