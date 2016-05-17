<?php

namespace App\PublicModule\Component;
use Doctrine\ORM\Query;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class Carousel extends \Nette\Application\UI\Control
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
	 * @var integer $id
	 * @see Nette\Application\Control#render()
	 */
	public function render($id)
	{
		$this->getTemplate()->id = $id;

		$this->getTemplate()->carousel = $this->carouselRepository()->find($id);
		$this->getTemplate()->setFile(__DIR__.'/templates/Carousel.latte');
		$this->getTemplate()->render();
	}

	public function carouselRepository()
	{
		return $this->em->getRepository('\App\Entity\Carousel');
	}
}
