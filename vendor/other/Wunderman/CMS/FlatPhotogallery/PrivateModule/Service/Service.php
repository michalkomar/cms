<?php
	/**
	 * Created by PhpStorm.
	 * User: horacekp
	 * Date: 27/01/16
	 * Time: 15:13
	 */

	namespace Wunderman\CMS\FlatPhotoGallery\PrivateModule;


	use App\PrivateModule\AttachmentModule\Model\Service\Attachment;
	use App\PrivateModule\PagesModule\Presenter\ComposedPageExtension;
	use Kdyby\Doctrine\EntityManager;
	use Nette\Application\UI\Form;
	use Nette\Http\Request;

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
		 * @type EntityManager
		 */
		private $em;

		/**
		 * Service constructor.
		 *
		 * @param Attachment $attachmentService
		 * @param Request $httpRequest
		 * @param EntityManager $em
		 */
		public function __construct(Attachment $attachmentService, Request $httpRequest, EntityManager $em)
		{
			$this->attachmentService = $attachmentService;
			$this->httpRequest = $httpRequest;
			$this->em = $em;
		}

		/**
		 * Prepare adding new item, add inputs to global form etc.
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

			$item->addSelect('item', 'New item', $this->readGalleriesArray())->setAttribute('data-values', $this->readGalleries());

			$item->setValues([], TRUE);
			$item->addHidden('type')->setValue('flatPhotoGallery');
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
			return ['id' => $values['item']];
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
			$params = $this->createParamsAssocArray($item->params);
			$gallery = $this->galleryRepository()->find($params['id']);
			return $gallery ? \Nette\Utils\Strings::webalize($gallery->name) : false;
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
		private function readGalleries()
		{
			$galleries = $this->galleryRepository()->createQueryBuilder('g')
				->select('g')
				->where('g.status = :statusOk')
				->setParameter('statusOk', 'ok')
				->getQuery()
				->getArrayResult();

			foreach ($galleries as &$gallery) {
				$galleryPhoto = $this->galleryItemRepository()->findOneBy(['flatPhotoGallery' => $gallery['id'], 'status' => 'ok'], ['position' => 'ASC']);
				$gallery['firstPhoto'] = isset($galleryPhoto->attachment->md5) ? $galleryPhoto->attachment->md5 : NULL;
			}

			return $galleries;
		}

		private function readGalleriesArray()
		{
			$galleries = $this->galleryRepository()->createQueryBuilder('g')
				->select('g')
				->where('g.status = :statusOk')
				->setParameter('statusOk', 'ok')
				->getQuery()->getArrayResult();

			$result = [];
			foreach ($galleries as $gallery) {
				$result[$gallery['id']] = $gallery['name'];
			}

			return $result;
		}

		/**
		 * @return \Kdyby\Doctrine\EntityRepository
		 */
		private function galleryRepository()
		{
			return $this->em->getRepository('\App\Entity\FlatPhotoGallery');
		}

		/**
		 * @return \Kdyby\Doctrine\EntityRepository
		 */
		private function galleryItemRepository()
		{
			return $this->em->getRepository('\App\Entity\FlatPhotoGalleryItem');
		}
	}
