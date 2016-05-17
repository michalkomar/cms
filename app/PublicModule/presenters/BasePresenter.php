<?php

namespace App\PublicModule\Presenters;

/**
 * BasePresenter
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class BasePresenter extends \App\Presenters\BasePresenter
{
	/** @var \Nette\DI\Container $container */
	private $container;

	/** @var array $config */
	private $config;

	/** @var \App\PublicModule\Component\Menu @menuControl */
	private $menuControl;

	/**
	 * @inject
	 * @var \Kdyby\Doctrine\EntityManager
	 */
	public $em;

	/**
	 * @inject
	 * @var \Kdyby\Events\EventManager
	 */
	public $evm;

	/**
	 * @inject
	 * @var \App\PublicModule\Component\GoogleTagManager
	 */
	public $googleTagManager;

	/**
	 * @inject
	 * @var \WebLoader\Nette\LoaderFactory
	 */
	public $webloaderLoaderFactory;

	public function startup()
	{
		parent::startup();
		$this->config = $this->container->parameters;
	}

	/**
	 * @return \App\PublicModule\Component\Menu
	 */
	public function createComponentMenu()
	{
		return $this->menuControl;
	}

	/**
	 * @return \App\PublicModule\Component\GoogleAnalytics
	 */
	public function createComponentGoogleTagManager()
	{
		return $this->googleTagManager;
	}

	/**
	 * @return \WebLoader\Nette\CssLoader
	 */
	public function createComponentCss()
	{
		return $this->webloaderLoaderFactory->createCssLoader('public');
	}

	public function createComponentJs() {
		return $this->webloaderLoaderFactory->createJavaScriptLoader('public');
	}


	// <editor-fold defaultstate="collapsed" desc="Geters">
	/**
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
	}
	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="DependencyInjection">
	/**
	 * Container injection
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @param \Nette\DI\Container $container
	 */
	public function injectContainerService(\Nette\DI\Container $container)
	{
		if ( $this->container !== NULL )
		{
			throw new \Nette\InvalidStateException('Container has already been set.');
		}
		$this->container = $container;
	}

	/**
	 * MenuControl injection
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @param \App\PublicModule\Component\Menu $menuControl
	 */
	public function injectMenuControlService(\App\PublicModule\Component\Menu $menuControl)
	{
		if ( $this->menuControl !== NULL )
		{
			throw new \Nette\InvalidStateException('MenuControl has already been set.');
		}
		$this->menuControl = $menuControl;
	}
	// </editor-fold>
}
