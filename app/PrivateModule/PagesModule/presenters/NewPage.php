<?php

namespace App\PrivateModule\PagesModule\Presenter;

/**
 * UsersPresenter
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class NewPagePresenter extends \App\PrivateModule\PrivatePresenter
{


	/**
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 */
	public function renderDefault($menuId = null)
	{
		$this->getTemplate()->menuId = $menuId;
	}
}
