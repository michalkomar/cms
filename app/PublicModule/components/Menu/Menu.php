<?php

namespace App\PublicModule\Component;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class Menu extends \Nette\Application\UI\Control
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/** @var \App\Entity\MenuItem */
	private $menuRepository;

	/** @var \App\Entity\MenuItem */
	private $menuItem;

	public function __construct(\Doctrine\ORM\EntityManager $em)
	{
		$this->em = $em;
		$this->menuRepository = $em->getRepository('\App\Entity\MenuItem');
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var integer $menuId
	 * @see Nette\Application\Control#render()
	 */
	public function render($menuId)
	{
		$this->getTemplate()->setFile(__DIR__.'/templates/Menu.latte');
		$this->getTemplate()->items =  $this->menuRepository->findBy(['menu' => $this->em->getReference('\App\Entity\Menu', $menuId), 'published' => 1, 'status' => 'ok'], ['lft'=>'ASC']);


		$this->getTemplate()->render();
	}
}
