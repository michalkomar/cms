<?php
	/**
	 * Created by PhpStorm.
	 * User: horacekp
	 * Date: 27/01/16
	 * Time: 15:13
	 */

	namespace Wunderman\CMS\Form\PrivateModule;


	use App\PrivateModule\AttachmentModule\Model\Service\Attachment;
	use App\PrivateModule\PagesModule\Presenter\ComposedPageExtension;
	use Kdyby\Doctrine\EntityManager;
	use Nette\Application\UI\Form;
	use Nette\Http\Request;
	use Nette\Utils\Arrays;

	class Service implements ComposedPageExtension
	{

		/**
		 * @var Attachment
		 */
		private $attachmentService;

		/**
		 * @var Request
		 */
		private $httpRequest;

		/**
		 * @var EntityManager
		 */
		private $em;

		/**
		 * Service constructor.
		 *
		 * @param Attachment $attachmentService
		 */
		public function __construct(Attachment $attachmentService, Request $httpRequest, EntityManager $em)
		{
			$this->attachmentService = $attachmentService;
			$this->httpRequest = $httpRequest;
			$this->em = $em;
		}

		/**
		 * Prepare adding new item, add imputs to global form etc.
		 *
		 * @param Form $button
		 *
		 * @return mixed
		 */
		public function addItem(Form &$form)
		{
			if(isset($form[self::ITEM_CONTAINER])) {
				unset($form[self::ITEM_CONTAINER]);
			}

			$item = $form->addContainer(self::ITEM_CONTAINER);
			$item->addHidden('itemType')->setValue('form');
			$item->addSelect('formName', 'Form name', $this->getForms());
			$item->setValues([], TRUE);

			$item->addHidden('itemId')->setValue(null);
			$item->addHidden('type')->setValue('form');
		}

		/**
		 * @param Form $form
		 *
		 * @return mixed
		 */
		public function editItemParams(Form &$form, $editItem)
		{
			$params = $this->createParamsAssocArray($editItem->getParams());

			$this->addItem($form);
//			$form[self::ITEM_CONTAINER]->addSelect('formName', 'Form', $this->getForms())->setValue(\Nette\Utils\Arrays::get($params, 'name', null));


			$form[self::ITEM_CONTAINER]->setDefaults([
				'itemId' => $editItem->id,
				'formName' => Arrays::get($params, 'name', null),
			]);
		}

		/**
		 * Make magic for creating new item, e.g. save new image and return his params for save.
		 *
		 * @var array $values Form values
		 *
		 * @return array Associated array in pair [ propertyName => value ] for store to the database
		 */
		public function processNew($values)
		{
			return ['name' => $values['formName']];
		}

		/**
		 * Editing current edited item
		 *
		 * @var array $values Form values
		 * @var array $itemParams
		 *
		 * @return array
		 */
		public function processEdit($values, $itemParams)
		{
			$itemParam = $this->composeArticleItemParamRepository()->findOneBy(['composeArticleItem' => $this->em->getReference('\App\Entity\ComposeArticleItem', $values['itemId']), 'name' => 'name']);
			$itemParam->setValue($values['item']);
		}

		/**
		 * @return array
		 * @TODO Implement automatic searching of forms
		 */
		private function getForms()
		{
			return ['ContactUs' => 'Contact Us'];
		}

		/**
		 * Compute anchor for item on the page
		 *
		 * @var object
		 *
		 * @return string
		 */
		public function getAnchor($item)
		{
			return 'form';
		}

		/**
		 * @return string
		 */
		public function getAddItemTemplate()
		{
			return realpath(__DIR__.'/../Templates/editItem.latte');
		}

		/**
		 * @return string
		 */
		public function getEditItemTemplate()
		{
			return $this->getAddItemTemplate();
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
		 * @return \Kdyby\Doctrine\EntityRepository
		 */
		public function composeArticleItemParamRepository()
		{
			return	$this->em->getRepository('\App\Entity\ComposeArticleItemParam');
		}
	}