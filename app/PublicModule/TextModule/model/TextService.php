<?php

namespace App\PublicModule\TextModule\Model\Service;
use App\Entity\InvalidUserException;
use App\Entity\NoCompanyException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kdyby\Doctrine\EntityManager;
use Nette\InvalidArgumentException;
use Nette\Security\User;

/**
 * Users service
 * @author Petr Horacek <petr.horacek@wunderman.cz>
 */
class Text extends \Nette\Object
{

	/**
	 * @var \Kdyby\Doctrine\EntityManager $entityManager
	 */
	private $entityManager;


	/**
	 * Construct
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 * @param \Kdyby\Doctrine\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function readArticle($id)
	{
		return $this->textArticleRepository()->createQueryBuilder('t')
					->select('t')
					->where('t.id = :id')
					->setParameter('id', $id)
					->getQuery()
					->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
	}



	// <editor-fold defaultstate="collapsed" desc="Repositories">

	public function textArticleRepository()
	{
		return $this->entityManager->getRepository('\App\Entity\TextArticle');
	}

	// </editor-fold>

}
