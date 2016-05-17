<?php

namespace App\PrivateModule\PagesModule\Component;
use Tracy\ILogger;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class ComponentWrapperFactory extends \Nette\Application\UI\Control
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/**
	 * @var array
	 */
	private $pageAttributes = [];

	/**
	 * @var \Tracy\ILogger
	 */
	public $logger;

	/**
	 * @var array
	 */
	public $allowedComponents;

	public function __construct($allowedComponents, \Kdyby\Doctrine\EntityManager $em, ILogger $logger)
	{
		$this->em = $em;
		$this->logger = $logger;
		$this->allowedComponents = $allowedComponents;
	}

	/**
	 * @param int $id
	 * @param array $params
	 *
	 * @return mixed
	 */
	public function create()
	{
		return new ComponentWrapper($this->em, $this->logger, $this->allowedComponents);
	}
}
