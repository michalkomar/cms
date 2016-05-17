<?php

namespace App\PrivateModule\UsersModule\Model\Service;
use App\Entity\InvalidUserException;
use App\service\Mailer;
use Kdyby\Doctrine\EntityManager;
use Nette\InvalidArgumentException;
use Nette\Security\User;

/**
 * Users service
 * @author Petr Horacek <petr.horacek@wunderman.cz>
 */
class Users extends \Nette\Object
{

	/** @var \Kdyby\Doctrine\EntityManager $entityManager */
	private $entityManager;

	/** @var \Nette\Security\User $user */
	private $user;

	/** @var \App\service\Mailer $mailer */
	private $mailer;

	/**
	 * Construct
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 * @param \App\PrivateModule\UsersModule\Model\Dao\Users $usersDao
	 */
	public function __construct(EntityManager $entityManager, User $user, Mailer $mailer)
	{
		$this->entityManager = $entityManager;
		$this->user = $user;
		$this->mailer = $mailer;
	}

	public function findUser($username)
	{
		return $this->userRespository()->findOneBy(array('username' => $username));
	}

	/**
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 * @param int $userId
	 * @return array
	 */
	public function readUser($criteria = null)
	{
		$qb = $this->entityManager->createQueryBuilder();

		$users = $this->userRespository()
					->createQueryBuilder('u')
					->select(array('partial u.{id, identityNo, name, surname, username, email, role, organization }, o, d'))
					->leftJoin('u.organization', 'o')
					->leftJoin('u.districts', 'd')
					->where('u.status = :statusOk')
					->setParameter('statusOk', 'ok');


		if (is_array($criteria) && isset($criteria['organization']))
		{
			$users->andWhere('u.organization = :organization')
				->setParameter('organization', (int) $criteria['organization']);
		}
		if (is_array($criteria) && isset($criteria['id']))
		{
			$users->andWhere('u.id = :id')
				->setParameter('id', (int) $criteria['id']);
		}

		if (is_array($criteria) && isset($criteria['username']))
		{
			$users->andWhere('u.username = :username')
				->setParameter('username', $criteria['username']);

			return $users->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
		}

		return $users->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
	}

	/**
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 * @param array $data
	 * @return int - inserted id
	 */
	public function createUser($data)
	{
		$user = new \App\Entity\User();

		$user->setName($data['name'])
			->setIdentityNo($data['identityNo'])
			->setSurname($data['surname'])
			->setUsername($data['username'])
			->setEmail($data['email'])
			->setPassword($data['password'])
			->setRole($data['role']);

		if (isset($data['districts']) && is_array($data['districts']))
		{
			$this->setUserDistricts($user, $data['districts']);
		}

		$this->entityManager->persist($user)->flush();

		return $this->userRespository()
			->createQueryBuilder('u')
			->select(array('partial u.{id, identityNo, name, surname, username, email, role, organization }, o, d'))
			->leftJoin('u.organization', 'o')
			->leftJoin('u.districts', 'd')
			->where('u.id = :id')
			->setParameter('id', $user->getId())
			->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

	}

	/**
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 * @param array $data
	 * @return DibiResult|int
	 * @throws \Nette\InvalidArgumentException
	 */
	public function updateUser($data)
	{
		if (isset($data['id']))
		{
			$user = $this->userRespository()->find($data['id']);

			if (!$user)
			{
				throw new InvalidArgumentException('User not found in database.');
			}

			if (isset($data['password']) and $data['password'] === $data['passwordRe'])
			{
				$user->setPassword($data['password']);
				$this->mailer->sendMail('resetPassword', 'O2 navigátor Horeca - Uživatelské heslo bylo změněno', $user->getEmail(), array(
					'password' => $data['password']
				));
			}

			$user->setRole($data['role']);

			if (isset($data['districts']) && is_array($data['districts']))
			{
				$this->setUserDistricts($user, $data['districts']);
			}

			$this->entityManager->persist($user)->flush();

			return $this->userRespository()
				->createQueryBuilder('u')
				->select(array('partial u.{id, identityNo, name, surname, username, email, role, organization }, o, d'))
				->leftJoin('u.organization', 'o')
				->leftJoin('u.districts', 'd')
				->where('u.id = :id')
				->setParameter('id', $data['id'])
				->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
		}

		throw new InvalidArgumentException('$data[\'id\'] not set. Can\'t find user!');
	}

	/**
	 * Reset user password
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 * @param $userId
	 * @return bool
	 */
	public function updateUserPassword($userId)
	{
		$user = $this->userRespository()->find($userId);

		if (! $user)
		{
			throw new InvalidUserException;
		}

		$password = $user->generateNewPassword();

		$this->mailer->sendMail('resetPassword', 'O2 navigátor Horeca - Reset uživatelského hesla', $user->getEmail(), array(
			'password' => $password
		));

		return array($password);
	}

	/**
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 *
	 * @param int	$userId
	 * @return bool
	 */
	public function destroyUser($userId)
	{
		$user = $this->userRespository()->find($userId);

		$user->destroy();
		$this->entityManager->persist($user)->flush();

		return true;
	}

	/**
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 * @return array
	 */
	public function readRoles()
	{
		if($this->user->isInRole('O2-backoffice') or $this->user->isInRole('O2-admin'))
		{
			return array(
				array('id' => 'O2-salesman', 			'label' => 'Obchodní zástupce'),
				array('id' => 'O2-scheef', 		'label' => 'Vedoucí'),
				array('id' => 'O2-backoffice', 	'label' => 'Backoffice'),
				array('id' => '02-admin', 		'label' => 'Administrátor')
			);
		}
		elseif($this->user->isInRole('brewery-backoffice') or $this->user->isInRole('brewery-admin'))
		{
			return array(
				array('id' => 'brewery-salesman', 		'label' => 'Obchodní zástupce'),
				array('id' => 'brewery-scheef', 	'label' => 'Vedoucí'),
				array('id' => 'brewery-backoffice', 'label' => 'Backoffice'),
				array('id' => 'brewery-admin', 		'label' => 'Administrátor')
			);
		}
		elseif($this->user->isInRole('admin'))
		{
			return array(
				array('id' => 'admin', 				'label' => 'Admin'),
				array('id' => 'O2-salesman', 				'label' => 'O2 - Obchodní zástupce'),
				array('id' => 'O2-scheef', 			'label' => 'O2 - Vedoucí'),
				array('id' => 'O2-backoffice', 		'label' => 'O2 - Backoffice'),
				array('id' => 'O2-admin', 			'label' => 'O2 - Administrátor'),
				array('id' => 'brewery-salesman', 		'label' => 'Pivovar - Obchodní zástupce'),
				array('id' => 'brewery-scheef', 	'label' => 'Pivovar - Vedoucí'),
				array('id' => 'brewery-backoffice', 'label' => 'Pivovar - Backoffice'),
				array('id' => 'brewery-admin', 		'label' => 'Pivovar - Administrátor')
			);
		}
	}

	/**
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 * @return array
	 */
	public function readChannels()
	{
		 return array(
			 array('id' => 'o2', 		'label' => 'O2'),
			 array('id' => 'brewery', 	'label' => 'Pivovar')
		 );
	}

	public function generatePasswordsForImportedUsers()
	{
		$users = $this->userRespository()->findBy(array('status' => 'ok', 'password' => 'a'));

		$credentials = [];

		foreach ($users as $user)
		{
			$credentials[$user->username] = $user->generateNewPassword();
		}

		$this->entityManager->flush();
		return $credentials;
	}

	// <editor-fold defaultstate="collapsed" desc="Repositories">

	private function userRespository()
	{
		return $this->entityManager->getRepository('\App\Entity\User');
	}

	public function mailRepository()
	{
		return $this->entityManager->getRepository('\App\Entity\Mail');
	}

	// </editor-fold>

}
