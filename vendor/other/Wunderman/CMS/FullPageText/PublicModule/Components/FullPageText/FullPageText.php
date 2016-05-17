<?php

namespace App\PublicModule\Component;
use Doctrine\ORM\Query;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class FullPageText extends \Nette\Application\UI\Control
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	public function __construct(\Kdyby\Doctrine\EntityManager $em)
	{
		$this->em = $em;
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var integer $textId
	 * @see Nette\Application\Control#render()
	 */
	public function render($params)
	{
		$this->getTemplate()->params = $params;
		$this->getTemplate()->text = $this->fullPageTextRepository()->find((int) $params['id']);
		$this->getTemplate()->setFile(__DIR__.'/templates/FullPageText.latte');
		$this->getTemplate()->render();
	}

	public function fullPageTextRepository()
	{
		return $this->em->getRepository('\App\Entity\FullPageText');
	}
}
