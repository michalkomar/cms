<?php

	namespace App\PrivateModule\BoxesModule\Presenter;

	use App\PrivateModule\BoxesModule\Model\Service;

	class NewPresenter extends \App\PrivateModule\PrivatePresenter
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

		public function actionDefault()
		{
			$box = $this->boxesModel->createNewBox();
			$this->redirect(':Private:Boxes:Edit:', array('id' => $box->id));
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