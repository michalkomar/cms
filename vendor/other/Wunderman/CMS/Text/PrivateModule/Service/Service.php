<?php
	/**
	 * Created by PhpStorm.
	 * User: horacekp
	 * Date: 27/01/16
	 * Time: 15:13
	 */

	namespace Wunderman\CMS\Text\PrivateModule;


	use App\PrivateModule\AttachmentModule\Model\Service\Attachment;
	use App\PrivateModule\PagesModule\Presenter\ComposedPageExtension;
	use Kdyby\Doctrine\EntityManager;
	use Nette\Application\UI\Form;
	use Nette\Http\Request;
	use App\Entity\ComposeArticleItemParam;

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
			$item->addHidden('itemType')->setValue('text');
			$item->addText('title', 'Title');
			$item->addTextArea('content', 'Content');

			$item->addSelect('template', 'Template', $this->getAvailableTextTemplates());
			$item->setValues([], TRUE);

			$item->addHidden('type')->setValue('text');
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
			$text = $this->textArticleRepository()->find((int) $params['id']);

			$this->addItem($form);

			$form[self::ITEM_CONTAINER]->setDefaults([
				'itemId' => $editItem->id,
				'title' => $text->title,
				'template' => isset($params['template']) ? $params['template'] : null,
				'content' => $text->content,
			]);
		}

		/**
		 * Make magic for creating new item, e.g. save new image and return his params for save.
		 * @var array $values Form values
		 * @return array Associated array in pair [ propertyName => value ] for store to the database
		 */
		public function processNew($values)
		{
			$text = new \App\Entity\TextArticle();
			$text->setTitle($values['title'])
				->setContent($values['content']);

			$this->em->persist($text)->flush();

			return [
				'id' => $text->getId(),
				'template' => $values->template
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
			\Tracy\Debugger::barDump('edit');
			$text = $this->textArticleRepository()->find($itemParams['id']);

			$text->setTitle($values['title'])
				->setContent($values['content']);

			return [
				'template' => $values['template'],
			];
		}

		/**
		 * Compute anchor for item on the page
		 * @var object
		 * @return string
		 */
		public function getAnchor($item)
		{
			$params = $this->createParamsAssocArray($item->params);
			$text = $this->textArticleRepository()->find((int) $params['id']);
			return $text ? \Nette\Utils\Strings::webalize($text->title) : false;
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
		 * @return array
		 */
		private function getAvailableTextTemplates()
		{
			$templates = \Nette\Utils\Finder::findFiles('*.latte')->from(realpath(__DIR__.'/../../PublicModule/Components/Text/templates/'));

			$availableTemplates = [];
			foreach ($templates as $template)
			{
				$template = explode('/', $template);
				$template = end($template);
				$templateName = str_replace('.latte', '', $template);
				$templateName = preg_split('/(?=[A-Z])/', $templateName, -1, PREG_SPLIT_NO_EMPTY);
				array_walk($templateName, function(&$item){
					$item = strtolower($item);
				});
				$templateName = implode(' ', $templateName);

				$availableTemplates[$template] = $templateName;
			}

			return $availableTemplates;
		}

		/**
		 * @return \Kdyby\Doctrine\EntityRepository
		 */
		public function textArticleRepository()
		{
			return $this->em->getRepository('\App\Entity\TextArticle');
		}

		/**
		 * @return \Kdyby\Doctrine\EntityRepository
		 */
		private function composeArticleItemParamRepository()
		{
			return $this->em->getRepository('\App\Entity\ComposeArticleItemParam');
		}
	}
