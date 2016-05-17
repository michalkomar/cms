<?php

namespace App\PrivateModule\PagesModule\Component;
use Kdyby\Doctrine\EntityManager;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class ContactMe extends \App\PublicModule\Component\ContactMe
{
	/**
	 * @var array
	 */
	protected $componentParams;

	public function __construct(EntityManager $em, $params)
	{
		parent::__construct($em);

		$this->componentParams = $params;
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var integer $textId
	 * @see Nette\Application\Control#render()
	 */
	public function render($id = null)
	{
		parent::render($this->componentParams->params);
	}
}
