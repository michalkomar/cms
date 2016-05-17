<?php

	namespace App\PrivateModule\FeedsModule\Presenter;

	use App\PrivateModule\FeedsModule\Model\Service\Feeds;

	class ListPresenter extends \App\PrivateModule\PrivatePresenter
	{
		/**
		 * @inject
		 * @var \Tracy\ILogger
		 */
		public $logger;

		/**
		 * @inject
		 * @var Feeds
		 */
		public $feedsModel;

		/**
		 * AJAX handler for switch flag for display feed item on social carousel.
		 *
		 * @param $id   ID of feed item
		 */
		public function handleUpdate($id)
		{
			$this->feedsModel->switchFlagForDisplayOnCarousel($id);
			$this->redrawControl('itemsContainer');
		}
		
		public function renderDefault()
		{
			$this->getTemplate()->feeds = $this->feedsModel->readFeeds();
		}

		// <editor-fold defaultstate="collapsed" desc="Repositories">
		// </editor-fold>
	}