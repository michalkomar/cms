<?php

	namespace App\PrivateModule\BoxesModule\Presenter;

	use App\PrivateModule\BoxesModule\Model\Service;

	class ListPresenter extends \App\PrivateModule\PrivatePresenter
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

		public function renderDefault()
		{
			$this->getTemplate()->boxes = $this->boxesModel->readBoxes();
		}

		public function handleRemoveItem($item)
		{
			try {
				$this->boxesModel->destroyBox($item);
				$this->flashMessage('Box has been deleted.', 'success');
			} catch (\Exception $e) {
				$this->logger->log($e);
				$this->flashMessage('Box cannot be deleted. Error was logged.', 'danger');
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