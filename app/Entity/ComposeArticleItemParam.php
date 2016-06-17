<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Petr Besir Horáček <sirbesir@gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="compose_article_item_param")
 */
class ComposeArticleItemParam extends \Kdyby\Doctrine\Entities\BaseEntity
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
	protected $value;

	/**
	 * @ORM\ManyToOne(targetEntity="ComposeArticleItem")
	 * @ORM\JoinColumn(name="compose_article_item_id", referencedColumnName="id")
	 */
	protected $composeArticleItem;

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
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return ComposeArticleItem
	 */
	public function getComposeArticleItem()
	{
		return $this->composeArticleItem;
	}

}
