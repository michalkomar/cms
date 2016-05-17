<?php
	/**
	 * Created by PhpStorm.
	 * User: horacekp
	 * Date: 27/01/16
	 * Time: 15:13
	 */

	namespace Wunderman\CMS\ThreeColumns\PrivateModule;


	use App\PrivateModule\AttachmentModule\Model\Service\Attachment;
	use App\PrivateModule\PagesModule\Presenter\ComposedPageExtension;
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
		 * @var string
		 */
		private $alt;

		/**
		 * @var string
		 */
		private $anchor;

		/**
		 * Service constructor.
		 *
		 * @param Attachment $attachmentService
		 */
		public function __construct(Attachment $attachmentService, Request $httpRequest)
		{
			$this->attachmentService = $attachmentService;
			$this->httpRequest = $httpRequest;
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
			if (isset($form[self::ITEM_CONTAINER])) {
				unset($form[self::ITEM_CONTAINER]);
			}

			$item = $form->addContainer(self::ITEM_CONTAINER);
			$item->addHidden('itemId')->setValue(NULL);
			$item->addText('anchor')->setValue($this->anchor);

			$item->addText('firstHeader');
			$item->addTextArea('firstContent');
			$item->addText('secondHeader');
			$item->addTextArea('secondContent');
			$item->addText('thirthHeader');
			$item->addTextArea('thirthContent');

			$item->addText('link');
			$item->addText('linkText');

			$item->addHidden('type')->setValue('threeColumns');
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

			$form[self::ITEM_CONTAINER]->setDefaults([
				'itemId' => $editItem->id,
				'anchor' => Arrays::get($params, 'anchor', NULL),
				'firstHeader' => Arrays::get($params, 'firstHeader', NULL),
				'firstContent' => Arrays::get($params, 'firstContent', NULL),
				'secondHeader' => Arrays::get($params, 'secondHeader', NULL),
				'secondContent' => Arrays::get($params, 'secondContent', NULL),
				'thirthHeader' => Arrays::get($params, 'thirthHeader', NULL),
				'thirthContent' => Arrays::get($params, 'thirthContent', NULL),

				'link' => Arrays::get($params, 'link', NULL),
				'linkText' => Arrays::get($params, 'linkText', NULL),
			]);
		}

		/**
		 * Make magic for creating new item, e.g. save new image and return his params for save.
		 * @var array $values Form values
		 * @return array Associated array in pair [ propertyName => value ] for store to the database
		 */
		public function processNew($values)
		{
			$file = $this->httpRequest->getFile(self::ITEM_CONTAINER)['image'];
			return [
				'id' => $file ? $this->attachmentService->processFile($file) : NULL,
				'anchor' => $values['anchor'],
				'firstHeader' => $values['firstHeader'],
				'firstContent' => $values['firstContent'],
				'secondHeader' => $values['secondHeader'],
				'secondContent' => $values['secondContent'],
				'thirthHeader' => $values['thirthHeader'],
				'thirthContent' => $values['thirthContent'],

				'link' => $values['link'],
				'linkText' => $values['linkText'],
			];
		}

		/**
		 * Editing current edited item
		 * @var array $values Form values
		 * @var array $itemParams
		 * @return array
		 */
		public function processEdit($values, $itemParams)
		{
			$file = $this->httpRequest->getFile(self::ITEM_CONTAINER)['image'];

			$result = [
				'anchor' => Arrays::get($values, 'anchor', NULL),
				'firstHeader' => Arrays::get($values, 'firstHeader', NULL),
				'firstContent' => Arrays::get($values, 'firstContent', NULL),
				'secondHeader' => Arrays::get($values, 'secondHeader', NULL),
				'secondContent' => Arrays::get($values, 'secondContent', NULL),
				'thirthHeader' => Arrays::get($values, 'thirthHeader', NULL),
				'thirthContent' => Arrays::get($values, 'thirthContent', NULL),

				'link' => Arrays::get($values, 'link', NULL),
				'linkText' => Arrays::get($values, 'linkText', NULL),
			];

			return $result;
		}

		/**
		 * Compute anchor for item on the page
		 * @var object
		 * @return string
		 */
		public function getAnchor($item)
		{
			$params = $this->createParamsAssocArray($item->params);
			return isset($params['anchor']) ? $params['anchor'] : FALSE;
		}

		/**
		 * @return string
		 */
		public function getAddItemTemplate()
		{
			return realpath(__DIR__ . '/../Templates/editItem.latte');
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
	}
