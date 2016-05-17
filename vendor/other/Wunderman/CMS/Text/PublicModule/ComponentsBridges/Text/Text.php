<?php

namespace App\PublicModule\ComposeModule\Component;
use Kdyby\Doctrine\EntityManager;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class Text extends \App\PublicModule\Component\Text
{
	/**
	 * @var array
	 */
	protected $componentParams;

	public function __construct(EntityManager $em, $componentParams)
	{
		parent::__construct($em);

		$this->componentParams = $componentParams;
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var integer $id - not used, only api compatibility
	 * @see Nette\Application\Control#render()
	 */
	public function render($id = null)
	{
		parent::render($this->componentParams['params']);
	}
}
