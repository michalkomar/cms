<?php

	namespace App\PrivateModule\FeedsModule\Presenter;

	use App\PrivateModule\FeedsModule\Model\Service;

	class NewPresenter extends \App\PrivateModule\PrivatePresenter
	{
		/**
		 * @inject
		 * @var \Tracy\ILogger
		 */
		public $logger;

		/**
		 * @inject
		 * @var Service\Feeds
		 */
		public $feedsModel;

		public function actionDefault()
		{
			$box = $this->feedsModel->createNewBox();
			$this->redirect(':Private:Feeds:Edit:', array('id' => $box->id));
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