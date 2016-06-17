<?php

namespace App\PrivateModule\PagesModule\Presenter;
use App\Entity\MenuItem;
use App\Entity\TextArticle;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;

/**
 * StandardPagePresenter
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class TextPagePresenter extends PagePresenter
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
	 * @var \App\Entity\TextArticle
	 */
	private $textArticle;

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
		$this->menuItemParams = \Nette\Utils\Json::decode($this->menuItem->params);
		$this->textArticle = $this->textArticleRepository()->find($this->menuItemParams->id);

		$this->defaults = array(
			'name' => $this->menuItem->name,
			'url' => $this->menuItem->url,
			'header' => $this->textArticle->title,
			'content' => $this->textArticle->content,
			'keywords' => $this->textArticle->keywords,
			'description' => $this->textArticle->description,
			'published' => $this->menuItem->published,
			'target' => $this->menuItem->target,
			'parent' => $this->getMenuPosition($this->menuItem),
			'menu' => is_null($this->menuItem->menu) ? null : $this->menuItem->menu->id
		);
	}

	/**
	 * @param $id
	 */
	public function renderEdit($id)
	{
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
	 * @param Form      $form
	 * @param ArrayHash $values
	 *
	 */
	public function editPage(Form $form, ArrayHash $values)
	{
		$this->menuItem
			->setPublished($values['published'])
			->setUrl($values['url'])
			->setTarget($values['target'])
			->setName($values['name']);

		$this->setMenuPosition($values['parent'], $this->menuItem);

		$this->textArticle->setTitle($values['header'])
			->setContent($values['content'])
			->setKeywords($values['keywords'])
			->setDescription($values['description']);

		$this->em->flush();
	}

	/**
	 * Create new page
	 * @param Form      $form
	 * @param ArrayHash $values
	 *
	 * @throws \Nette\Utils\JsonException
	 */
	public function createPage(Form $form, ArrayHash $values)
	{
		$this->em->beginTransaction();
		$this->textArticle = new TextArticle();
		$this->textArticle->setTitle($values['header'])
			->setKeywords($values['keywords'])
			->setDescription($values['description'])
			->setContent($values['content']);

		$this->em->persist($this->textArticle);
		$this->em->flush();

		$menuItemParams = ['id' => $this->textArticle->id];
		ksort($menuItemParams);
		$this->menuItem = new MenuItem();
		$this->menuItem->setName($values['name'])
			->setUrl($values['url'])
			->setPublished($values['published'])
			->setTarget($values['target'])
			->setPresenter('Public:Text:Text')
			->setAction('default')
			->setParams(\Nette\Utils\Json::encode($menuItemParams));

		$this->em->persist($this->menuItem);
		$this->em->flush();

		$this->setMenuPosition($values['parent'], $this->menuItem);

		$this->em->commit();

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
