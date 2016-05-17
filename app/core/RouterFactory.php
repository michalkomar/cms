<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{

	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/**
	 * @type \Kdyby\Doctrine\EntityRepository
	 */
	private $menuItemRepository;

	/**
	 * RouterFactory constructor.
	 *
	 * @param \Kdyby\Doctrine\EntityManager $em
	 */
	public function __construct(\Kdyby\Doctrine\EntityManager $em)
	{
		$this->injectEntityManager($em);
		$this->menuItemRepository = $this->em->getRepository('\App\Entity\MenuItem');
	}

	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();
		$public = new RouteList('Public');
		$admin = new RouteList('Private');

		$public[] = new Route('/cron/<module>/<presenter>[/<action>]', [], Route::ONE_WAY);

		$admin[] = new Route('administration/<module>/<presenter>[/<action>][/<id>]',
			[
				'module' => 'Dashboard',
				'presenter' => 'Dashboard',
				'action' => 'default',
			]);

		$databaseUriRoute = new DatabaseMenuRoute('<uri .*>', [
			'module' => 'Compose',
			'presenter' => 'Compose',
			'action' => 'default',
		]);

		$databaseAppRoute = new DatabaseMenuRoute('<module>/<presenter>/<action>[/<id>]', [
			'module' => 'Compose',
			'presenter' => 'Compose',
			'action' => 'default',
		]);
		$databaseUriRoute->injectMenuItemDao($this->menuItemRepository);
		$databaseAppRoute->injectMenuItemDao($this->menuItemRepository);

		$router[] = $admin;

		$router[] = new Route('registrace', 'Public:User:Register:default');
		$router[] = new Route('zapomenute-heslo', 'Public:User:LostPassword:default');
		$router[] = new Route('zapomenute-heslo/nove-heslo', 'Public:User:LostPassword:reset');

		$router[] = $public;
		$router[] = $databaseUriRoute;
		$router[] = $databaseAppRoute;

		return $router;
	}

	// <editor-fold defaultstate="collapsed" desc="DependencyInjection">
	/**
	 * EntityManager injection
	 * @param \Kdyby\Doctrine\EntityManager $em
	 */
	public function injectEntityManager(\Kdyby\Doctrine\EntityManager $em)
	{
		if ( $this->em !== NULL )
		{
			throw new \Nette\InvalidStateException('EntityManager has already been set.');
		}
		$this->em = $em;
	}
	// </editor-fold>

}
