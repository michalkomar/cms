<?php

namespace App\PublicModule\Component;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class GoogleTagManager extends \Nette\Application\UI\Control
{
	/** @var \App\Entity\MenuItem */
	private $tmKey;

	public function __construct($tmKey)
	{
		$this->tmKey = $tmKey;
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @see Nette\Application\Control#render()
	 */
	public function render()
	{
		$this->getTemplate()->tmKey = $this->tmKey;
		$this->getTemplate()->setFile(__DIR__.'/templates/GoogleTagManager.latte');
		$this->getTemplate()->render();
	}
}
