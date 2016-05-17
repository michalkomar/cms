<?php

namespace App\PublicModule\Component;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class GoogleAnalytics extends \Nette\Application\UI\Control
{
	/** @var \App\Entity\MenuItem */
	private $gaKey;

	public function __construct($tmKey)
	{
		$this->gaKey = $tmKey;
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @see Nette\Application\Control#render()
	 */
	public function render()
	{
		$this->getTemplate()->gaKey = $this->gaKey;
		$this->getTemplate()->setFile(__DIR__.'/templates/GoogleAnalytics.latte');
		$this->getTemplate()->render();
	}
}
