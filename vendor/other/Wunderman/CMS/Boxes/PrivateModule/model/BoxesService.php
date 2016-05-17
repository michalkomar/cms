<?php

namespace App\PrivateModule\BoxesModule\Model\Service;
use App\Entity\Box;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityManager;

/**
 * Users service
 * @author Petr Horacek <petr.horacek@wunderman.cz>
 */
class Boxes extends \Nette\Object
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

	public function readBoxes()
	{
		return $this->boxRepository()->findBy(array('status' => 'ok'));
	}

	public function readBox($id)
	{
		return $this->boxRepository()->findOneBy(array('status' => 'ok', 'id' => $id));
	}

	public function createNewBox()
	{
		$box = (new Box())->setName('newBox');
		$this->em->persist($box)->flush();
		return $box;
	}

	public function readBoxItem($id)
	{
		return $this->boxItemRepository()->findOneBy(array('id' => $id));
	}

	public function destroyBox($id)
	{
		$box = $this->boxRepository()->find($id);
		$box->destroy();
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

	public function boxRepository()
	{
		return $this->em->getRepository('\App\Entity\Box');
	}

	public function boxItemRepository()
	{
		return $this->em->getRepository('\App\Entity\BoxItem');
	}

	// </editor-fold>

}
