<?php
	/**
	 * Created by PhpStorm.
	 * User: horacekp
	 * Date: 27/01/16
	 * Time: 15:13
	 */

	namespace Wunderman\CMS\YoutubeVideo\PrivateModule;


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
			$item->addText('imageAlt')->setValue($this->alt);
			$item->addText('anchor')->setValue($this->anchor);
			$item->addUpload('image')->addCondition(Form::FILLED)->addRule(Form::IMAGE,
				'File must be image of type jpg, png or gif.');
			$item->addText('youtubeHash');
			$item->addRadioList('playButtonPosition', 'Play button position', [
				'1' => 1,
				'2' => 2,
				'3' => 3,
				'4' => 4,
				'5' => 5,
				'6' => 6,
				'7' => 7,
				'8' => 8,
				'9' => 9,
				'10' => 10,
				'11' => 11,
				'12' => 12,
				'center' => 'center',
			]);
			$item->addText('playButtonVerticalPosition');
			$item->addCheckbox('deleteImage');
			$item->addCheckbox('playOnLoad');
			$item->setValues([
				"playButtonPosition" => 'center',
				"playButtonVerticalPosition" => 0,
			], TRUE);

			$form->onValidate[] = [$this, 'validateYoutubeHash'];

			$item->addHidden('type')->setValue('youtubeVideo');
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
				'imageAlt' => Arrays::get($params, 'imageAlt', NULL),
				'anchor' => Arrays::get($params, 'anchor', NULL),
				'youtubeHash' => Arrays::get($params, 'youtubeHash', NULL),
				'playButtonPosition' => Arrays::get($params, 'playButtonPosition', NULL),
				'playButtonVerticalPosition' => Arrays::get($params, 'playButtonVerticalPosition', NULL),
				'playOnLoad' => Arrays::get($params, 'playOnLoad', NULL),
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
				'imageAlt' => $values['imageAlt'],
				'anchor' => $values['anchor'],
				'youtubeHash' => $this->getYoutubeHash($values['youtubeHash']),
				'playButtonPosition' => $values['playButtonPosition'],
				'playButtonVerticalPosition' => $values['playButtonVerticalPosition'],
				'playOnLoad' => $values['playOnLoad'],
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
				'imageAlt' => Arrays::get($values, 'alt', NULL),
				'anchor' => Arrays::get($values, 'anchor', NULL),
				'youtubeHash' => $this->getYoutubeHash(Arrays::get($values, 'youtubeHash')),
				'playButtonPosition' => Arrays::get($values, 'playButtonPosition', NULL),
				'playButtonVerticalPosition' => Arrays::get($values, 'playButtonVerticalPosition', NULL),
				'playOnLoad' => Arrays::get($values, 'playOnLoad', NULL),
			];

			if ($file) {
				$result['id'] = $file ? $this->attachmentService->processFile($file) : Arrays::get($itemParams,
					'itemId', NULL);
			} elseif (! $file && isset($values['deleteImage'])) {
				$result['id'] = NULL;
			}

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

		/**
		 * @param $string
		 *
		 * @return mixed
		 */
		public function validateYoutubeHash(Form $form)
		{
			$values = $form->getValues();

			if (! $this->getYoutubeHash($values->item->youtubeHash))
			{
				$form->addError('Not valid YoutubeHash or Youtube url.');
			}
		}

		/**
		 * @param $string
		 *
		 * @return bool|mixed
		 */
		private function getYoutubeHash($string)
		{
			$matches = [];
			preg_match("/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/m", $string, $matches);

			if ($matches)
			{
				return Arrays::get($matches, 1, FALSE);
			}
			elseif (! preg_match('#[/\\\\]#', $string))
			{
				return $string;
			}

			return FALSE;
		}
	}