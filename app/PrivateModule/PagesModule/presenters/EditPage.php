<?php

namespace App\PrivateModule\PagesModule\Presenter;

/**
 * UsersPresenter
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class EditPagePresenter extends \App\PrivateModule\PrivatePresenter
{

	/**
	 * @inject
	 * @var \Kdyby\Doctrine\EntityManager
	 */
	public $em;

	public function actionDefault($id)
	{
		$page = $this->menuItemRepository()->find($id);

		switch ($page->presenter) {
			case 'Public:Text:Text':
				$this->redirect(':Private:Pages:TextPage:edit', array('id' => $id));
				break;
			case 'Public:Compose:Compose':
				$this->redirect(':Private:Pages:Compose:edit', array('id' => $id));
				break;
			case 'ExternalUrl':
				$this->redirect(':Private:Pages:UrlPage:edit', array('id' => $id));
				break;
		}

	}

	public function menuItemRepository()
	{
		return $this->em->getRepository('\App\Entity\MenuItem');
	}
}
