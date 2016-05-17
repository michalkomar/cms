<?php

	use Doctrine\ORM\NoResultException;
	use Nette\Security;

/**
 * Users authenticator.
 * @author Petr Besir Horacek <sirbesir@gmail.com>
 */
class DatabaseAuthenticator extends Nette\Object implements Security\IAuthenticator
{
	/**
	 * @var \Kdyby\Doctrine\EntityManager
	 */
	public $entityManager;

	/**
	 * @param \Kdyby\Doctrine\EntityManager $entityManager
	 */
	public function __construct(\Kdyby\Doctrine\EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		try {
			$user = $this->userRespository()
							->createQueryBuilder('u')
							->select('u')
							->where('u.username = :username')
							->setParameter('username', $username)
							->getQuery()
							->getSingleResult();
		} catch (NoResultException $e) {
			throw new Security\AuthenticationException('Špatné přihlašovací údaje.', self::INVALID_CREDENTIAL);
		}

		if (($user->password !== \App\Entity\User::calculateHash($password, $user->password))) {
			throw new Security\AuthenticationException('Špatné přihlašovací údaje.', self::INVALID_CREDENTIAL);
		}

		if ($user->isDel())
		{
			throw new Security\AuthenticationException('Uživatelský účet byl smazán.', self::IDENTITY_NOT_FOUND);
		}

		if (is_null($user->role) or $user->role === '')
		{
			throw new \Nette\InvalidArgumentException('Uživatelský účet nemá nastavenou žádnou roli.');
		}

		return new Security\Identity($user->id, $user->role, array(
			'name' => $user->getName(),
			'surname' => $user->getSurname(),
			'email' => $user->getEmail(),
			'role' => $user->getRole()
		));
	}

	/**
	 * @return \Kdyby\Doctrine\EntityRepository
	 */
	private function userRespository()
	{
		return $this->entityManager->getRepository('\App\Entity\User');
	}

}
