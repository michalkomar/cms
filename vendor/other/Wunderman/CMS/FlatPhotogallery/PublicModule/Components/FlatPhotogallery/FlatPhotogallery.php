<?php

namespace App\PublicModule\Component;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class FlatPhotoGallery extends \Nette\Application\UI\Control
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/**
	 * @var array
	 */
	private $gallery;

	/**
	 * @var array
	 */
	private $categories;

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
	public function render($id)
	{
		$this->getTemplate()->id = $id;

		$this->getGallery($id);

		$this->getTemplate()->categories = $this->categories;
		$this->getTemplate()->gallery = $this->gallery;
		$this->getTemplate()->setFile(__DIR__.'/templates/FlatPhotoGallery.latte');
		$this->getTemplate()->render();
	}

	private function getGallery($id)
	{
		try {
			$this->gallery = $this->galleryRepository()->createQueryBuilder('b')
				->select('b, items, attachment')
				->join('b.items', 'items')
				->leftJoin('items.attachment', 'attachment')
				->where('b.id = :id')
				->andWhere('b.status = :statusOk')
				->andWhere('items.status = :statusOk')
				->setParameter('id', $id)
				->setParameter('statusOk', 'ok')
				->orderBy('items.position')
				->getQuery()
				->getSingleResult(Query::HYDRATE_ARRAY);
		} catch (NoResultException $e) {
			$this->gallery = FALSE;
		}
	}

	private function galleryRepository()
	{
		return $this->em->getRepository('\App\Entity\FlatPhotoGallery');
	}
}
