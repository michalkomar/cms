<?php

namespace Wunderman\CMS\PrivateModule\PagesModule\Component;
use Kdyby\Doctrine\EntityManager;
use Tracy\ILogger;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class Form extends \Nette\Application\UI\Control
{

	/**
	 * @var object
	 */
	private $componentParams;

	public function __construct(EntityManager $em, $componentParams, ILogger $logger)
	{
		$params = [];
		foreach ($componentParams->params as $param)
		{
			$params[$param->name] = $param->value;
		}

		$this->componentParams = $params;
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var integer $textId
	 * @see Nette\Application\Control#render()
	 */
	public function render()
	{
		$this->getTemplate()->componentParams = $this->componentParams;
		$this->getTemplate()->setFile(__DIR__.'/templates/default.latte');
		$this->getTemplate()->render();
	}
}
