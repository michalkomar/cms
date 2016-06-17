<?php

namespace App\PrivateModule\PagesModule\Presenter;

use App\Entity\MenuItem;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\InvalidArgumentException;
use Nette;

class PagePresenter extends \App\PrivateModule\PrivatePresenter implements IPage
{
	/**
	 * @inject
	 * @var \Tracy\ILogger
	 */
	public $logger;

	/**
	 * @inject
	 * @var \Kdyby\Doctrine\EntityManager
	 */
	public $em;

	/**
	 * @type array
	 */
	private $defaults = [];

	/**
	 * @var \App\Entity\MenuItem
	 */
	private $menuItem;

	/**
	 * @var array
	 */
	private $menuItemParams;

	/**
	 * @var \Nette\Application\UI\Form
	 */
	private $form;

	public function createBaseForm($name)
	{
		$form = new Form($this, $name);

		$form->addText('name', 'Name')
			->setRequired('Fill Page settings -> name');
		$form->addText('url', 'Url')
			->setRequired('Fill Page settings -> url');

		$form->addCheckbox('published', 'Published')
			->setDefaultValue(0);

		$form->addCheckbox('homepage', 'Published')
			->setDefaultValue(0);

		$form->addSelect('target', 'Target', [
			'_self' => 'In the same window',
			'_blank' => 'In the new window'
		])->setDefaultValue('_self');

		$form->addSelect('parent', 'Have a parent page?', $this->getPages())
			->setPrompt('Page without menu');

		$form->addSubmit('save', 'Save')->onClick[] = array($this, 'savePage');

		return $form;
	}

	/**
	 * @param $id
	 *
	 * @throws \Nette\Utils\JsonException
	 */
	public function actionEdit($id)
	{
	}

	/**
	 * @param $id
	 */
	public function renderEdit($id)
	{
	}

	/**
	 * @param null|int $menuId
	 */
	public function renderDefault($menuId = null)
	{
	}

	public function actionDelete($id)
	{
		$this->menuItemRepository()->find($id)->setUrl(null)->delete();
		$this->em->flush();
		$this->flashMessage('The page was deleted.', 'success');
		$this->redirect(':Private:Dashboard:Dashboard:');
	}

	/**
	 * Creating page form
	 * @return Form
	 */
	public function createComponentPageForm($name)
	{
		$form = new Form($this, $name);

		$form->addText('name', 'Name')
			->setRequired('Fill Page settings -> name');
		$form->addText('url', 'Url')
			->setRequired('Fill Page settings -> url');

		$form->addText('header', 'Header');

		$form->addTextArea('content', 'Content');

		$form->addCheckbox('published', 'Published')
			->setDefaultValue(0);

		$form->addText('keywords', 'Keywords');
		$form->addText('description', 'Description');

		$form->addSelect('parent', 'Have a parent page?', $this->getPages())
			->setPrompt('Page without menu');

		$form->addSelect('menu', 'Menu', $this->getMenus())
			->setPrompt('Select menu');

		$form->addSubmit('save', 'Save')->onClick[] = array($this, 'savePage');

		$form->setDefaults($this->defaults);

		return $this->form = $form;
	}

	/**
	 * Save page routine
	 * @param Form $form
	 */
	public function savePage(SubmitButton $button)
	{
		try {
			if ($this->getAction() === 'edit')
			{
				$this->editPage($form->getValues());
			}
			elseif ($this->getAction() === 'default')
			{
				$this->createPage($form->getValues());
			}

			$this->getPresenter()->flashMessage('Page was saved.', 'success');
			$this->getPresenter()->redirect('this');
		}
		catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
			if( $e->getSQLState() === '23000' )
			{
				if( \preg_match( "/for key '?([a-zA-Z]+)'?/", $e->getMessage(), $match ) )
				{
					$this->form->addError('Unique constraint failed for key "' . $match[1] . '".');
				}
			}
			else
			{
				$this->logger->log($e);
				$this->form->addError('An unexcepted error, please try later. This error was logged. '.$e->getSQLState());
			}
		}
	}

	/**
	 * Update current opened page
	 */
	public function editPage(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values)
	{
	}

	/**
	 * Create new page
	 */
	public function createPage(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values)
	{
	}

	/**
	 * @param $parent
	 *
	 * @throws \Doctrine\ORM\ORMException
	 */
	public function setMenuPosition($parent, &$menu)
	{
		if (is_null($parent))
		{
			$menu->setParent(null)->setMenu(null);
		}
		elseif (\preg_match( "/first-([0-9]+)/", $parent, $match ))
		{
			$menu->setParent(null)->setMenu($this->em->getReference('\App\Entity\Menu', (int) $match[1]));
		}
		else
		{
			$newParent = $this->menuItemRepository()->find((int) $parent);
			$menu->setParent($newParent)->setMenu($newParent->menu);
		}
	}

	/**
	 * @param MenuItem $menuItem
	 *
	 * @return null|string
	 */
	public function getMenuPosition(MenuItem $menuItem)
	{
		if (!is_null($menuItem->getMenu()))
		{
			if (is_null($menuItem->getParent()) or $menuItem->getDepth() === 0)
			{
				return 'first-'.$menuItem->getMenu()->getId();
			}
			elseif ($menuItem->getDepth() > 0)
			{
				return $menuItem->getParent()->getId();
			}
		}
		else
		{
			return null;
		}

		throw new InvalidArgumentException("Menu Item {$menuItem->getId()} have any error in tree setting.");
	}

	/**
	 * @return array
	 */
	public function getPages()
	{
		$pages = [];

		/**
		 * Adding pages with menu
		 */
		$menus = $this->menuRepository()->findBy(array('published' => 1), array('name' => 'ASC'));
		foreach ($menus as $menu)
		{
			$pages[$menu->name]['first-'.$menu->id] = '+ Root at '.$menu->name;
			foreach ($menu->getItems() as $item)
			{
				if ($item->id != $this->getParameter('id'))
				{
					$pages[$menu->name][$item->id] = str_repeat(" » ", $item->depth) . $item->name;
				}
			}
		}

		/**
		 * Adding pages without menu
		 */
		$menuItemsWithoutMenu = $this->menuItemRepository()->findBy(array('menu' => null, 'status' => 'ok'), array('lft' => 'ASC'));
		if (count($menuItemsWithoutMenu))
		{
			foreach ($menuItemsWithoutMenu as $item)
			{
				if ($item->id != $this->getParameter('id'))
				{
					$pages['Pages without menu'][$item->id] = str_repeat(" » ", $item->depth) . $item->name;
				}
			}
		}

		return $pages;
	}

	/**
	 * @param MenuItem $newHomepage
	 */
	protected function resetHomepage(MenuItem $newHomepage)
	{
		$homepages = $this->menuItemRepository()->findBy(['homepage' => 1]);

		if (count($homepages))
		{
			foreach ($homepages as $item) {
				$item->setHomepage(FALSE);
			}

		}

		$newHomepage->setHomepage(TRUE);
	}

	/**
	 * Return all menus
	 * @return array
	 */
	public function getMenus()
	{
		return $this->menuRepository()->findPairs('name', 'id');
	}

	// <editor-fold defaultstate="collapsed" desc="Repositories">
	public function menuItemRepository()
	{
		return $this->em->getRepository('\App\Entity\MenuItem');
	}

	public function menuRepository()
	{
		return $this->em->getRepository('\App\Entity\Menu');
	}
	// </editor-fold>
}