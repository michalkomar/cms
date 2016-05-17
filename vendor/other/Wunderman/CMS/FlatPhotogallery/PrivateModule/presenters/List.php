<?php

	namespace App\PrivateModule\FlatPhotoGalleryModule\Presenter;

	use App\PrivateModule\FlatPhotoGalleryModule\Model\Service\FlatPhotoGallery;

	class ListPresenter extends \App\PrivateModule\PrivatePresenter
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

		public function renderDefault()
		{
			$this->getTemplate()->galleries = $this->galleryModel->readGalleries();
		}

		public function handleRemoveItem($item)
		{
			try {
				$this->galleryModel->destroyGallery($item);
				$this->flashMessage('Gallery has been deleted.', 'success');
			} catch (\Exception $e) {
				$this->logger->log($e);
				$this->flashMessage('Gallery cannot be deleted. Error was logged.', 'danger');
			}
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