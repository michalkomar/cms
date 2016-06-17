<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Petr Besir Horáček <sirbesir@gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="compose_article")
 */
class ComposeArticle extends \Kdyby\Doctrine\Entities\BaseEntity
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
	protected $title;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $keywords;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $description;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $content;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $notes;

	/**
	 * @ORM\Column(type="enum")
	 */
	protected $status = 'ok';

	/**
	 * @ORM\OneToMany(targetEntity="ComposeArticleItem", mappedBy="composeArticle")
	 */
	protected $items;

	/**
	 * @param string $status
	 *
	 * @return ArrayCollection|ComposeArticleItem[]
	 */
	public function getItems($status = 'ok')
	{
		$criteria = Criteria::create();

		$criteria->where(Criteria::expr()->eq('status', $status))
			->orderBy(['position' => 'ASC']);

		$items = $this->items->matching($criteria);

		$return = [];

		foreach ($items as $item) {
			$return[$item->getId()] = $item;
		}

		return $return;
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
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @return mixed
	 */
	public function getKeywords()
	{
		return $this->keywords;
	}

	/**
	 * @return mixed
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @return mixed
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @return mixed
	 */
	public function getNotes()
	{
		return $this->notes;
	}

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return $this->status;
	}
}
