<?php

	namespace App\PrivateModule\FlatPhotoGalleryModule\Presenter;

	use App\PrivateModule\FlatPhotoGalleryModule\Model\Service\FlatPhotoGallery;

	class NewPresenter extends \App\PrivateModule\PrivatePresenter
	{
		/**
		 * @inject
		 * @var FlatPhotoGallery
		 */
		public $galleryModel;

		public function actionDefault()
		{
			$gallery = $this->galleryModel->createNewGallery();
			$this->redirect(':Private:FlatPhotoGallery:Edit:', array('id' => $gallery->id));
		}
	}
