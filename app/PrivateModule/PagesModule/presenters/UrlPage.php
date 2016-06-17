<?php

namespace App\PrivateModule\PagesModule\Presenter;

use App\Entity\MenuItem;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;

/**
 * StandardPagePresenter
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class UrlPagePresenter extends PagePresenter
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
	 * @var \Nette\Application\UI\Form
	 */
	private $form;

	/**
	 * @param $id
	 *
	 * @throws \Nette\Utils\JsonException
	 */
	public function actionEdit($id)
	{
		$this->menuItem = $this->menuItemRepository()->find($id);

		$this->defaults = array(
			'name' => $this->menuItem->name,
			'url' => $this->menuItem->url,
			'target' => $this->menuItem->target,
			'published' => $this->menuItem->published,
			'parent' => $this->getMenuPosition($this->menuItem),
			'menu' => is_null($this->menuItem->menu) ? null : $this->menuItem->menu->id
		);
	}

	/**
	 * @param $id
	 */
	public function renderEdit($id)
	{
		$this->getTemplate()->menuItem = $this->menuItem;
		$this->setView('default');
	}

	/**
	 * Creating page form
	 * @return Form
	 */
	public function createComponentPageForm($name)
	{
		$this->form = $this->createBaseForm($name);

		$this->form->addText('header', 'Header');

		$this->form->addTextArea('content', 'Content');

		$this->form->addCheckbox('showHeader', 'Show header on page')
			->setDefaultValue(1);

		$this->form->addText('keywords', 'Keywords');
		$this->form->addText('description', 'Description');

		$this->form->setDefaults($this->defaults);

		return $this->form;
	}

	/**
	 * Save page routine
	 * @param Form $form
	 */
	public function savePage(SubmitButton $button)
	{
		$form = $button->getForm();

		try {
			if ($this->getAction() === 'edit')
			{
				$this->editPage($form->getValues());
			}
			elseif ($this->getAction() === 'default')
			{
				$this->createPage($form->getValues());
			}

			$this->flashMessage('Page was saved.', 'success');
			$this->redirect('this');
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
	 * @param $values
	 *
	 * @throws \Doctrine\ORM\ORMException
	 */
	public function editPage($values)
	{
		$this->menuItem
			->setPublished($values['published'])
			->setUrl($values['url'])
			->setTarget($values['target'])
			->setName($values['name']);

		$this->setMenuPosition($values['parent'], $this->menuItem);

		$this->em->flush();
	}

	/**
	 * Create new page
	 * @param $values
	 *
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Nette\Utils\JsonException
	 */
	public function createPage($values)
	{
		$this->menuItem = new MenuItem();
		$this->menuItem->setName($values['name'])
			->setUrl($values['url'])
			->setPublished($values['published'])
			->setPresenter('ExternalUrl')
			->setAction('default')
			->setParams(null);

		$this->setMenuPosition($values['parent'], $this->menuItem);

		$this->em->persist($this->menuItem);
		$this->em->flush();

		$this->flashMessage('The page was saved.', 'success');
		$this->redirect('edit', array('id' => $this->menuItem->id));
	}

	// <editor-fold defaultstate="collapsed" desc="Repositories">
	public function textArticleRepository()
	{
		return $this->em->getRepository('\App\Entity\TextArticle');
	}
	// </editor-fold>
}
