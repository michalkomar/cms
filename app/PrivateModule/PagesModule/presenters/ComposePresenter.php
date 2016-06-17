<?php

namespace App\PrivateModule\PagesModule\Presenter;

use App\Entity\ComposeArticle;
use App\Entity\ComposeArticleItem;
use App\Entity\ComposeArticleItemParam;
use App\Entity\MenuItem;
use App\PrivateModule\PagesModule\Component\ComponentWrapperFactory;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\InvalidArgumentException;
use \Nette\Application\UI\Multiplier;
use Nette\Utils\ArrayHash;
use Nette\Utils\Arrays;
use Nette\Utils\Json;
use App\PrivateModule\ComposeModule\Exception\ComposePresenterException;
use Nette;

/**
 * StandardPagePresenter
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
final class ComposePresenter extends PagePresenter implements IPage
{

	const SESSION_SECTION = 'composedPageTmp';

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
	 * @var \Nette\Application\UI\Form
	 */
	private $form;

	/**
	 * @var \App\Entity\MenuItem
	 */
	private $menuItem;

	/**
	 * @var \App\Entity\ComposeArticle
	 */
	private $composeArticle;

	/**
	 * @var array
	 */
	private $formDefaults = [];

	/**
	 * @var null|\App\Entity\ComposeArticleItem
	 */
	private $editItem;

	/**
	 * @var array
	 */
	private $addButtons;

	/**
	 * @var array
	 */
	private $extensions = [];

	/**
	 * @var IExtensionService
	 */
	private $extensionService;

	/**
	 * @inject
	 * @var ComponentWrapperFactory
	 */
	public $componentsWrapperFactory;

	/**
	 * @var array
	 */
	private $composeComponentFactories = [];

	/**
	 * @var NULL|array
	 */
	private $composeArticleItems = NULL;


	/**
	 * @param $addButtons
	 */
	public function __construct($addButtons)
	{
		$this->addButtons = $addButtons;
	}

	/**
	 * Prepare basic settings for composed page
	 */
	public function startup()
	{
		parent::startup();
		$this->getTemplate()->itemContainer = IExtensionService::ITEM_CONTAINER;
	}
	
	/**
	 * @param $id
	 *
	 * @throws \Nette\Utils\JsonException
	 */
	public function actionEdit($id)
	{
		$this->menuItem = $this->menuItemRepository()->find($id);
		if (is_null($this->menuItem))
		{
			throw new BadRequestException('Menu item not found.', 404);
		}

		$params = \Nette\Utils\Json::decode($this->menuItem->getParams());
		$this->composeArticle = $this->composeArticleRepository()->find($params->id);

		if (is_null($this->composeArticle))
		{
			throw new InvalidArgumentException('Composed page not found for Menu Item '.$this->menuItem->getName());
		}

		$this->composeArticleItems = $this->composeArticle->getItems();
	}


	/**
	 * @param $id
	 */
	public function renderEdit($id)
	{
		$this->formDefaults = [
			'name' => $this->menuItem->getName(),
			'url' => $this->menuItem->getUrl(),
			'parent' => $this->getMenuPosition($this->menuItem),
			'published' => $this->menuItem->getPublished(),
			'target' => $this->menuItem->getTarget(),
			'keywords' => $this->composeArticle->getKeywords(),
			'description' => $this->composeArticle->getDescription(),
			'homepage' => $this->menuItem->getHomepage(),
		];

		$this->getTemplate()->article = $this->composeArticle;
		$this->getTemplate()->composeArticleItems = $this->composeArticleItems;
		$this->getTemplate()->menuItem = $this->menuItem;

		$presenter = $this;
		$this->getTemplate()->getParagraph = function($item) use ($presenter) {
			$this->setService($item->type);
			return $this->getService()->getAnchor($item);
		};

		$this->setView('default');
	}

	/**
	 * @param int $item
	 */
	public function handleMoveItemUp($item)
	{
		$item = $this->composeArticleItemRepository()->find($item);
		$item->setPosition($item->position -1);
		$this->em->flush();

		if ($this->isAjax())
		{
			$this->redrawControl('items');
		}
		else
		{
			$this->redirect('this');
		}
	}

	/**
	 * @param int $item
	 */
	public function handleMoveItemDown($item)
	{
		$item = $this->composeArticleItemRepository()->find($item);
		$item->setPosition($item->position +1);
		$this->em->flush();

		if ($this->isAjax())
		{
			$this->redrawControl('items');
		}
		else
		{
			$this->redirect('this');
		}
	}

	/**
	 * @param int $item
	 */
	public function handleEditItem($item)
	{
		$this->editItem = $this->composeArticleItemRepository()->find((int) $item);
		$this->setService($this->editItem->type);

		if ( ! $this->getService()->getEditItemTemplate())
		{
			throw new \RuntimeException("Item editTemplate path is not exists. Check the implementation of ".get_class($this->getService())."::getEditItemTemplate().");
		}
		$this->getTemplate()->editItemTemplate = $this->getService()->getEditItemTemplate();
		$this->getTemplate()->editedItem = $this->editItem;
	}

	/**
	 * @param int $item
	 */
	public function handleRemoveItem($item)
	{
		$item = $this->composeArticleItemRepository()->find($item);
		$item->remove();
		$this->em->flush();

		if ($this->isAjax())
		{
			$this->redrawControl('items');
		}
		else
		{
			$this->redirect('this');
		}
	}

	/**
	 * @param null $menuId
	 */
	public function actionDefault($menuId = null)
	{
	}

	/**
	 * Creating page form
	 * @return Form
	 */
	public function createComponentPageForm($name)
	{
		$this->form = $this->createBaseForm($name);

		$this->registerExtensionsButtons();

		if ($this->getHttpRequest()->getPost(IExtensionService::ITEM_CONTAINER) && !isset($this->form[IExtensionService::ITEM_CONTAINER]))
		{
			$this->setService($this->getHttpRequest()->getPost(IExtensionService::ITEM_CONTAINER)['type']);
			$this->addNewItem($this->getHttpRequest()->getPost(IExtensionService::ITEM_CONTAINER)['type']);
		}

		$this->form->addText('keywords', 'Keywords');
		$this->form->addText('description', 'Description');

		if (!is_null($this->formDefaults))
		{
			$this->form->setDefaults($this->formDefaults);
		}

		if (!is_null($this->editItem))
		{
			$this->addEditItemParams();
		}

		return $this->form;
	}

	/**
	 * Iterate on $this->addButtons and add this to global ComposedPageForm with services registered in Extensions
	 */
	private function registerExtensionsButtons()
	{
		$form = $this->form;
		$addButtons = $form->addContainer('addButtons');
		foreach ($this->addButtons as $key => $button) {
			$presenter = $this;

			if (is_array($button))
			{
				$buttonText = Arrays::get($button, 'text', FALSE);
				$tooltip = Arrays::get($button, 'tooltip', FALSE);
			}
			elseif (is_string($button))
			{
				$buttonText = $button;
			}

			$addButton = $addButtons->addSubmit($key, $buttonText);
			$addButton->setValidationScope(FALSE)->onClick[] = function (SubmitButton $button
			) use ($key, $form, $presenter) {
				$this->editItem = null;

				$form = $button->getForm();

				$presenter->setService($key);

				if ( ! $presenter->getService()->getAddItemTemplate())
				{
					throw new \RuntimeException("Item addTemplate path is not exists. Check the implementation of ".get_class($presenter->getService())."::getAddItemTemplate().");
				}

				$presenter->getTemplate()->addItemTemplate = $presenter->getService()->getAddItemTemplate();
				$presenter->getService()->addItem($form);
			};

			if (isset($tooltip)) $addButton->setAttribute('title', $tooltip);
		}
	}

	/**
	 * @return IExtensionService
	 */
	private function getService()
	{
		return $this->extensionService;
	}

	/**
	 * @param $serviceName
	 *
	 * @return $this
	 * @throws InvalidExtensionType
	 */
	private function setService($serviceName)
	{
		/**
		 * @var IExtensionService
		 */
		$this->extensionService = Arrays::get($this->extensions, $serviceName, FALSE);

		if (! $this->extensionService) {
			throw new InvalidExtensionType("Service '{$serviceName}' not found.");
		} elseif (! $this->extensionService instanceof IExtensionService) {
			throw new InvalidExtensionType("Service '{$serviceName}' is not instance of ComposedPageExtension.");
		}

		return $this;
	}

	private function addEditItemParams()
	{
		if(isset($this->form[IExtensionService::ITEM_CONTAINER])) {
			unset($this->form[IExtensionService::ITEM_CONTAINER]);
		}

		$this->getService()->editItemParams($this->form, $this->editItem);
	}

	/**
	 * @return Multiplier
	 */
	public function createComponentComponentWrapper()
	{
		return new  Multiplier(function() {
			return $this->componentsWrapperFactory->create();
		});
	}


	public function createComponent($name)
	{
		try {
			list($article, $factory) = $this->getComposeComponentFactory($name);

			return $factory->create($article->getParams());
		} catch (ComposePresenterException $e) {
			return parent::createComponent($name);
		}
	}


	public function setComposeComponentFactory($name, $factory)
	{
		$this->composeComponentFactories[$name] = $factory;
	}


	private function getComposeComponentFactory($name)
	{
		if (is_numeric($name)) {
			$id = (int) $name;
			$type = $this->composeArticleItems[$id]->getType();

			if (!isset($this->composeComponentFactories[$type])) {
				throw new \InvalidArgumentException(
					"Component with type [$type] does not exist."
				);
			}

			return [$this->composeArticleItems[$id], $this->composeComponentFactories[$type]];
		}

		throw new ComposePresenterException;
	}


	/**
	 * @param string $type
	 */
	public function addNewItem($type)
	{
		if(isset($this->form[IExtensionService::ITEM_CONTAINER])) {
			unset($this->form[IExtensionService::ITEM_CONTAINER]);
		}

		$service = $this->getService();
		$service->addItem($this->form);
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
				$this->editPage($form, $form->getValues());
			}
			elseif ($this->getAction() === 'default')
			{
				$this->createPage($form, $form->getValues());
			}

			$this->getPresenter()->flashMessage('Page was saved.', 'success');
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
	public function editPage(Form $form, ArrayHash $values)
	{
		$this->em->beginTransaction();

		// 1. update compose_article
		$this->composeArticle->setTitle($values['name'])
			->setKeywords($values['keywords'])
			->setDescription($values['description']);

		if (! $container = $this->getHttpRequest()->getPost(IExtensionService::ITEM_CONTAINER, false) OR Arrays::get($container, 'itemId', FALSE) === '' )
		{
			// 2. Create new item for page
			$this->saveNewItem($this->composeArticle, $form, $values);
		}
		else
		{
			// 3. Edit page item
			$this->editPageItem($form, $form->getValues()[IExtensionService::ITEM_CONTAINER]);
		}


		// 4. update menu_item
		$this->menuItem
			->setPublished($values['published'])
			->setUrl($values['url'])
			->setTarget($values['target'])
			->setName($values['name']);
		$this->setMenuPosition($values['parent'], $this->menuItem);

		if ($values['homepage'] == 'on')
		{
			$this->resetHomepage($this->menuItem);
		}

		$this->em->flush();
		$this->em->commit();

		$this->redirect('this');
	}

	/**
	 * Create new page
	 *
	 * @param $values
	 *
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Nette\Utils\JsonException
	 */
	public function createPage(Form $form, ArrayHash $values)
	{
		$this->em->beginTransaction();

		// 1. Create compose_article
		$composeArticle = new \App\Entity\ComposeArticle();
		$composeArticle->setKeywords($values['keywords'])
				->setDescription($values['description'])
				->setTitle($values['name']);
		$this->em->persist($composeArticle)->flush();

		// 2. Create new item for page
		$this->saveNewItem($composeArticle, $form, $values);

		// 3. last Create menu_item
		$menuItemParams = ['id' => $composeArticle->id];
		ksort($menuItemParams);
		$menuItem = new MenuItem();
		$menuItem->setName($values['name'])
				->setUrl($values['url'])
				->setPublished($values['published'])
				->setTarget($values['target'])
				->setPresenter('Public:Compose:Compose')
				->setAction('default')
				->setParams(\Nette\Utils\Json::encode($menuItemParams));
		$this->setMenuPosition($values['parent'], $menuItem);

		if ($values['homepage'] == 'on')
		{
			$this->resetHomepage($menuItem);
		}

		$this->em->persist($menuItem);

		$this->em->flush();
		$this->em->commit();

		$this->flashMessage('Page was created.');
		$this->redirect('edit', array('id' => $menuItem->id));
	}

	/**
	 * @param ComposeArticle $composeArticle
	 * @param $values
	 */
	public function saveNewItem(ComposeArticle $composeArticle, Form $form, $values)
	{
		$service = $this->getService();
		if ($service && isset($values[$service::ITEM_CONTAINER]))
		{
			$params = $service->processNew($form, $values[$service::ITEM_CONTAINER]);

			// 1. Create compose_article_item
			$composeArticleItem = new ComposeArticleItem();
			$composeArticleItem->setType($values[$service::ITEM_CONTAINER]['type'])
				->setComposeArticle($composeArticle);
			$this->em->persist($composeArticleItem);

			// 2. Create compose_article_item_param
			foreach ($params as $key => $value)
			{
				$param = new ComposeArticleItemParam();
				$param->setName($key)->setValue($value)->setComposeArticleItem($composeArticleItem);
				$this->em->persist($param);
			}
		}
	}

	/**
	 * @param array $values
	 */
	private function editPageItem(Form $form, ArrayHash $values)
	{
		$service = $this->getService();
		if ($values && $service)
		{
			$this->editItem = $this->composeArticleItemRepository()->find($values['itemId']);

			/**
			 * Creating association array with all page params
			 */
			$params = [];
			foreach ($this->editItem->getParams() as $param)
			{
				$params[$param->name] = $param->value;
			}

			$newParams = $service->processEdit($form, $values, $params);
			$params = array_merge($params, $newParams);

			foreach ($params as $key => $paramValue) {
				$itemParam = $this->composeArticleItemParamRepository()->findOneBy(['name' => $key, 'composeArticleItem' => $this->editItem->id]);

				if ($itemParam)
				{
					$itemParam->setValue($paramValue);
					$this->em->persist($itemParam);
				}
				else
				{
					$param = new ComposeArticleItemParam();

					$menuItemParams = Json::decode($this->menuItem->params, Json::FORCE_ARRAY);

					if (! Arrays::get($menuItemParams, 'id', false))
					{
						throw new \InvalidArgumentException('This menu Item have not parameter id required for composed page type.');
					}
					$param->setName($key)->setValue($paramValue)->setComposeArticleItem($this->em->getReference('\App\Entity\ComposeArticleItem', $this->editItem->id));
					$this->em->persist($param);
				}
			}

		}
	}

	/**
	 * @param $params
	 *
	 * @return array
	 */
	private function createParamsAssocArray($params)
	{
		$assocParams = [];
		foreach ($params as $param)
		{
			$assocParams[$param->name] = $param->value;
		}

		return $assocParams;
	}

	/**
	 * @param Object $service
	 * @param $extensionName
	 *
	 * @return bool
	 */
	public function addExtensionService($extensionName, IExtensionService $service)
	{
		$this->extensions[$extensionName] = $service;
		return TRUE;
	}

	// <editor-fold defaultstate="collapsed" desc="Repositories">
	/**
	 * @return \Kdyby\Doctrine\EntityRepository
	 */
	public function composedMenuItemRepository()
	{
		return	$this->em->getRepository('\App\Entity\MenuItem');
	}

	/**
	 * @return \Kdyby\Doctrine\EntityRepository
	 */
	public function composeArticleRepository()
	{
		return	$this->em->getRepository('\App\Entity\ComposeArticle');
	}

	/**
	 * @return \Kdyby\Doctrine\EntityRepository
	 */
	public function composeArticleItemRepository()
	{
		return	$this->em->getRepository('\App\Entity\ComposeArticleItem');
	}

	public function composeArticleItemParamRepository()
	{
		return	$this->em->getRepository('\App\Entity\ComposeArticleItemParam');
	}
	// </editor-fold>
}
