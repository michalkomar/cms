<?php

	namespace App\PrivateModule\BoxesModule\Presenter;

	use App\PrivateModule\BoxesModule\Component\EditItemForm;
	use App\PrivateModule\BoxesModule\Model\Service;
	use Nette\Application\UI\Form;

	class EditPresenter extends \App\PrivateModule\PrivatePresenter
	{
		/**
		 * @inject
		 * @var \Tracy\ILogger
		 */
		public $logger;

		/**
		 * @inject
		 * @var Service\Boxes
		 */
		public $boxesModel;

		/**
		 * @var \App\Entity\Box
		 */
		public $box;

		/**
		 * @var \App\Entity\BoxItem
		 */
		private $item;

		/**
		 * @inject
		 * @var \App\PrivateModule\AttachmentModule\Model\Service\Attachment
		 */
		public $attachmentService;

		/**
		 * @param $id
		 */
		public function actionDefault($id, $editItemId = NULL, $do = NULL)
		{
			$this->getTemplate()->box = $this->box = $this->boxesModel->readBox($id);
		}

		/**
		 * @return Form
		 */
		public function createComponentBoxPreferencesForm()
		{
			$form = new Form();
			$form->addSubmit('save', 'Save');
			$form->addText('name', 'Name')->setDefaultValue($this->box->name === 'newBox' ?: $this->box->name);
			$form->addCheckbox('showNavigation', 'Show navigation')->setDefaultValue($this->box->showNavigation);
			$form->addCheckbox('showFilters', 'Show filters')->setDefaultValue($this->box->showFilters);

			$form->onSuccess[] = array($this, 'editPreferences');

			return $form;
		}

		/**
		 * @param Form $form
		 */
		public function editPreferences(Form $form)
		{
			$values = $form->getValues();
			$this->box->setName($values->name)->setShowNavigation($values->showNavigation)->setShowFilters($values->showFilters);
			$this->em->flush();

			$this->flashMessage('Box was saved.', 'success');

			if ($this->isAjax()) {
				$this->redrawControl('boxEditForm');
				$this->redrawControl('flashes');
			} else {
				$this->redirect('this');
			}
		}

		public function handleEditBoxItem()
		{
			$this->item = $this->boxesModel->readBoxItem($this->getParameter('editItemId'));
		}

		public function handleMoveRight()
		{
			$item = $this->boxesModel->readBoxItem($this->getParameter('moveItemId'));
			$item->setPosition($item->position + 1);
			$this->em->flush();

			if ($this->isAjax()) {
				$this->redrawControl('editFormItem');
			}
		}

		public function handleMoveLeft()
		{
			$item = $this->boxesModel->readBoxItem($this->getParameter('moveItemId'));
			$item->setPosition($item->position - 1);
			$this->em->flush();

			if ($this->isAjax()) {
				$this->redrawControl('editFormItem');
			}
		}

		public function createComponentEditItem()
		{
			return new EditItemForm($this->attachmentService, $this->boxesModel, $this->box,
				$this->getParameter('editItemId'));
		}

		public function createComponentNewItem()
		{
			return new EditItemForm($this->attachmentService, $this->boxesModel, $this->box);
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