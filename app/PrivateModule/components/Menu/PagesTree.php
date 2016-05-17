<?php

namespace App\PrivateModule\Component;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class PagesTree extends \Nette\Application\UI\Control
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/** @var \App\Entity\MenuItem */
	private $menuItemRepository;

	public function __construct(\Doctrine\ORM\EntityManager $em)
	{
		$this->em = $em;
		$this->menuItemRepository = $em->getRepository('\App\Entity\MenuItem');
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var integer $menuId
	 * @see Nette\Application\Control#render()
	 */
	public function render()
	{
		$this->getTemplate()->setFile(__DIR__.'/templates/Menu.latte');
		$this->getTemplate()->items = $this->menuItemRepository->findBy(array(), array('menu' => 'DESC', 'lft' => 'ASC'));

		$this->getTemplate()->menus = $this->em->getRepository('\App\Entity\Menu')->findBy(array(), array('name' => 'ASC'));

		$this->getTemplate()->render();
	}
}
