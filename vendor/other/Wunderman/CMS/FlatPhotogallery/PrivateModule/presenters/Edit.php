<?php

	namespace App\PrivateModule\FlatPhotoGalleryModule\Presenter;

	use App\PrivateModule\FlatPhotoGalleryModule\Model\Service\FlatPhotoGallery;
	use Nette\Application\UI\Form;
	use Nette\Utils\Json;

	class EditPresenter extends \App\PrivateModule\PrivatePresenter
	{
		/**
		 * @inject
		 * @var \Tracy\ILogger
		 */
		public $logger;

		/**
		 * @inject
		 * @var FlatPhotoGallery
		 */
		public $galleryModel;

		/**
		 * @var \App\Entity\Box
		 */
		public $gallery;

		/**
		 * @param $id
		 */
		public function actionDefault($id, $editItemId = NULL, $do = NULL)
		{
			$this->gallery = $this->galleryModel->readGallery($id);

			if (! $this->gallery)
			{
				$this->flashMessage('This gallery not found.', 'danger');
			}

		}

		public function renderDefault()
		{
			$this->getTemplate()->gallery = $this->gallery;
			$this->getTemplate()->images = Json::encode($this->galleryModel->readGalleryItems($this->gallery->id));
		}

		/**
		 * @return Form
		 */
		public function createComponentGalleryPreferencesForm()
		{
			$form = new Form();
			$form->addSubmit('save', 'Save');
			$form->addText('name', 'Name')->setDefaultValue($this->gallery->name === 'newBox' ?: $this->gallery->name);

			$form->onSuccess[] = array($this, 'editPreferences');

			return $form;
		}

		/**
		 * @param Form $form
		 */
		public function editPreferences(Form $form)
		{
			$values = $form->getValues();
			$this->gallery->setName($values->name);
			$this->em->flush();

			$this->flashMessage('Gallery was saved.', 'success');

			if ($this->isAjax()) {
				$this->redrawControl('boxEditForm');
				$this->redrawControl('flashes');
			} else {
				$this->redirect('this');
			}
		}

		public function handleUploadImage()
		{
			$this->sendJson($this->galleryModel->saveImageToGallery('image', $this->gallery));
		}

		public function handleUpdateImage()
		{
			$updatedImage = $this->galleryModel->updateImage($this->getHttpRequest()->getPost('id'), $this->getHttpRequest()->getPost());
			$this->sendJson($updatedImage);
		}

		public function handleDestroyImage()
		{
			$images = $this->galleryModel->destroyImage($this->getRequest()->getPost('id'));
			$this->sendJson($images);
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