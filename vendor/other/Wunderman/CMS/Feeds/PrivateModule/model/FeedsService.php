<?php

namespace App\PrivateModule\FeedsModule\Model\Service;
use App\Entity\Box;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityManager;

/**
 * Users service
 * @author Petr Horacek <petr.horacek@wunderman.cz>
 */
class Feeds extends \Nette\Object
{

	/**
	 * @var \Kdyby\Doctrine\EntityManager $em
	 */
	public $em;

	/**
	 * Construct
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 * @param \Kdyby\Doctrine\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->em = $entityManager;
	}

	public function readFeeds()
	{
		return $this->feedItemRepository()->createQueryBuilder('f')->select('f');
	}

	/**
	 * Switch flag for displaying feed item on carousel
	 *
	 * @param $id
	 * @return EntityManager
	 */
	public function switchFlagForDisplayOnCarousel($id)
	{
		/** @var $entity \App\Entity\FeedItem */
		$entity = $this->em->getRepository('\App\Entity\FeedItem')->find($id);
		$entity->isDisplay = !$entity->isDisplay;

		$this->em->persist($entity);
		$this-> em->flush();
	}

	// <editor-fold defaultstate="collapsed" desc="Repositories">
	/**
	 * @return \Kdyby\Doctrine\EntityRepository
	 */
	public function feedItemRepository()
	{
		return $this->em->getRepository('\App\Entity\FeedItem');
	}
	// </editor-fold>

}
