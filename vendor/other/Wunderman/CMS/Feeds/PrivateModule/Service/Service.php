<?php
	/**
	 * Created by PhpStorm.
	 * User: horacekp
	 * Date: 27/01/16
	 * Time: 15:13
	 */

	namespace Wunderman\CMS\Feeds\PrivateModule;


	use App\PrivateModule\AttachmentModule\Model\Service\Attachment;
	use App\PrivateModule\PagesModule\Presenter\ComposedPageExtension;
	use Kdyby\Doctrine\EntityManager;
	use Nette\Application\UI\Form;
	use Nette\Http\Request;
	use App\Entity\ComposeArticleItemParam;

	class Service implements ComposedPageExtension
	{

		/**
		 * @type EntityManager
		 */
		private $em;

		/**
		 * Service constructor.
		 *
		 * @param EntityManager $em
		 */
		public function __construct(EntityManager $em)
		{
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

//			$item->addSelect('item', 'New item', $this->readFeeds());
//
//			$item->setValues([], TRUE);
			$item->addHidden('type')->setValue('feeds');
			$item->addHidden('itemId')->setValue(null);
		}

		/**
		 * @param Form $form
		 *
		 * @return mixed
		 */
		public function editItemParams(Form &$form, $editItem)
		{
			$params = $this->createParamsAssocArray($editItem->params);

			$this->addItem($form);

			$form[self::ITEM_CONTAINER]->setDefaults([
				'itemId' => $editItem->id,
				'item' => $params['id']
			]);
		}

		/**
		 * Make magic for creating new item, e.g. save new image and return his params for save.
		 * @var array $values Form values
		 * @return array Associated array in pair [ propertyName => value ] for store to the database
		 */
		public function processNew($values)
		{
			return [];
		}

		/**
		 * Editing current edited item
		 * @var array $values Form values
		 * @var array $itemParams
		 * @return array
		 */
		public function processEdit($values, $itemParams)
		{
			return ['id' => $values['item']];
		}

		/**
		 * Compute anchor for item on the page
		 * @var object
		 * @return string
		 */
		public function getAnchor($item)
		{
			return NULL;
		}

		/**
		 * @return string
		 */
		public function getAddItemTemplate()
		{
			return realpath(__DIR__ . '/../templates/editItem.latte');
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
			foreach ($params as $param) {
				$assocParams[$param->name] = $param->value;
			}

			return $assocParams;
		}

		/**
		 * @return array
		 */
		public function readFeeds()
		{
			return $this->em->getRepository('\App\Entity\FeedItem')->findPairs(['status' => 'ok'], 'detail', [], 'id');
		}

		/**
		 * @return \Kdyby\Doctrine\EntityRepository
		 */
		public function feedsRepository()
		{
			return $this->em->getRepository('\App\Entity\FeedItem');
		}
	}
