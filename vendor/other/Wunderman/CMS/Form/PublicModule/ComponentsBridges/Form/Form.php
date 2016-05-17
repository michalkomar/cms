<?php

namespace App\PublicModule\ComposeModule\Component;
use Kdyby\Doctrine\EntityManager;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class Form extends \App\PublicModule\Component\Form
{

	/**
	 * @inject
	 * @var \Tracy\ILogger
	 */
	public $logger;

	public function __construct(EntityManager $em, $componentParams, $logger)
	{
		$this->logger = $logger;
		parent::__construct($em, $componentParams, $this->logger);
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var integer $textId
	 * @see Nette\Application\Control#render()
	 */
	public function render()
	{
		parent::render();
	}
}
