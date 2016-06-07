<?php

namespace App\PublicModule\ComposeModule\Model\Service;
use App\Entity\InvalidUserException;
use App\Entity\NoCompanyException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kdyby\Doctrine\EntityManager;
use Nette\InvalidArgumentException;
use Nette\Security\User;

/**
 * Users service
 * @author Petr Horacek <petr.horacek@wunderman.cz>
 */
class Compose extends \Nette\Object
{

	/**
	 * @var \Kdyby\Doctrine\EntityManager $em
	 */
	private $em;


	/**
	 * Construct
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 * @param \Kdyby\Doctrine\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->em = $entityManager;
	}

	public function readArticle($id)
	{
		return $this->composeArticleRepository()->createQueryBuilder('t')
					->select('t')
					->where('t.id = :id')
					->setParameter('id', $id)
					->getQuery()
					->getSingleResult(Query::HYDRATE_ARRAY);
	}

	public function readArticleParts($id)
	{
		$items = $this->composeArticleItemRepository()->createQueryBuilder('p')
				->select('p, params')
				->leftJoin('p.params', 'params')
				->where('p.composeArticle = :composeArticle')
				->setParameter('composeArticle', $this->em->getReference($this->composeArticleRepository()->getClassName(), $id))
				->andWhere('p.status = :statusOk')
				->setParameter('statusOk', 'ok')
				->orderBy('p.position', 'ASC')
				->getQuery()
				->getResult(Query::HYDRATE_ARRAY);

		$itemsById = [];

		foreach ($items as $item) {
			$itemsById[$item['id']] = $item;
		}

		foreach ($itemsById as &$item)
		{
			$params = [];

			foreach ($item['params'] as $param)
			{
				$params[$param['name']] = $param['value'];
			}

			$item['params'] = $params;
		}

		return $itemsById;
	}

	public function readPartParams($id)
	{
		$item = $this->composeArticleItemRepository()->createQueryBuilder('i')->select('i, p')
			->where('i.id = :id')
			->leftJoin('i.params', 'p')
			->setParameter('id', $id)
			->getQuery()->getArrayResult()[0];

		$params = [];
		foreach ($item['params'] as $param)
		{
			$params[$param['name']] = $param['value'];
		}

		$item['params'] = $params;

		return $item;
	}



	// <editor-fold defaultstate="collapsed" desc="Repositories">

	public function composeArticleRepository()
	{
		return $this->em->getRepository('\App\Entity\ComposeArticle');
	}

	public function composeArticleItemRepository()
	{
		return $this->em->getRepository('\App\Entity\ComposeArticleItem');
	}

	public function composeArticleItemParamRepository()
	{
		return $this->em->getRepository('\App\Entity\ComposeArticleItemParam');
	}

	// </editor-fold>

}
