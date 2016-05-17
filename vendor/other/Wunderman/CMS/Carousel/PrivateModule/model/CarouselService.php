<?php

namespace App\PrivateModule\CarouselModule\Model\Service;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityManager;

/**
 * Users service
 * @author Petr Horacek <petr.horacek@wunderman.cz>
 */
class Carousel extends \Nette\Object
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

	public function readCarousels()
	{
		return $this->carouselRepository()->findBy(array('status' => 'ok'));
	}

	public function readCarousel($id)
	{
		return $this->carouselRepository()->findOneBy(array('status' => 'ok', 'id' => $id));
	}

	public function createNewCarousel()
	{
		$box = (new \App\Entity\Carousel())->setName('newCarousel')->setShowNavigation(1);
		$this->em->persist($box)->flush();
		return $box;
	}

	public function readCarouselItem($id)
	{
		return $this->carouselItemRepository()->findOneBy(array('id' => $id));
	}

	public function destroyCarousel($id)
	{
		$carousel = $this->carouselRepository()->find($id);
		$carousel->destroy();
		$this->em->flush();
	}

	/**
	 * @param $entity
	 *
	 * @return EntityManager
	 */
	public function persist($entity)
	{
		return $this->em->persist($entity);
	}

	/**
	 * @param $entity
	 *
	 * @return EntityManager
	 */
	public function flush($entity = null)
	{
		return $this->em->flush($entity);
	}

	// <editor-fold defaultstate="collapsed" desc="Repositories">

	public function carouselRepository()
	{
		return $this->em->getRepository('\App\Entity\Carousel');
	}

	public function carouselItemRepository()
	{
		return $this->em->getRepository('\App\Entity\CarouselItem');
	}

	// </editor-fold>

}
