<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;

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
	 * @return mixed
	 */
	public function getItems($status = 'ok')
	{
		$criteria = Criteria::create();
		$criteria->where(Criteria::expr()->eq('status', $status))->orderBy(array('position' => 'ASC'));

		return $this->items->matching($criteria);
	}
}
