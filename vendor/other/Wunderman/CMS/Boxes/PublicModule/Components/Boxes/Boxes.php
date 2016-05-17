<?php

namespace App\PublicModule\Component;
use Doctrine\ORM\Query;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class Boxes extends \Nette\Application\UI\Control
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/**
	 * @var array
	 */
	private $box;

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

		$this->getBoxes($id);

		$this->getTemplate()->categories = $this->categories;
		$this->getTemplate()->box = $this->box;
		$this->getTemplate()->setFile(__DIR__.'/templates/Boxes.latte');
		$this->getTemplate()->render();
	}

	public function getBoxes($id)
	{
		$this->box = $this->boxRepository()->createQueryBuilder('b')
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

		$this->categories = [];
		foreach ($this->box['items'] as $item)
		{
			if ($item['category'] != false && $item['category'] != null && $item['category'] != '')
			$this->categories[$item['category']] = $item['category'];
		}
	}

	public function boxRepository()
	{
		return $this->em->getRepository('\App\Entity\Box');
	}
}
