<?php

namespace App\PublicModule\Component;
use Doctrine\ORM\Query;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class Feeds extends \Nette\Application\UI\Control
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	private $items;

	public function __construct(\Kdyby\Doctrine\EntityManager $em)
	{
		$this->em = $em;
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var integer $textId
	 * @see Nette\Application\Control#render()
	 */
	public function render()
	{
		$this->getFeeds();

		$this->getTemplate()->items = $this->items;
		$this->getTemplate()->setFile(__DIR__.'/templates/FeedsBox.latte');
		$this->getTemplate()->render();
	}

	public function getFeeds()
	{
		$this->items = $this->feedRepository()
			->createQueryBuilder('b')
			->select('b')
			->where('b.isDisplay = :isDisplay')
			->andWhere('b.status = :statusOk')
			->setParameter('isDisplay', TRUE)
			->setParameter('statusOk', 'ok')
			->getQuery()
			->getArrayResult(Query::HYDRATE_ARRAY);
	}

	public function feedRepository()
	{
		return $this->em->getRepository('\App\Entity\FeedItem');
	}
}
