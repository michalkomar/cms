<?php

	namespace App\PrivateModule\CarouselModule\Presenter;
	use App\PrivateModule\CarouselModule\Model\Service;

class NewPresenter extends \App\PrivateModule\PrivatePresenter
{
	/**
	 * @inject
	 * @var \Tracy\ILogger
	 */
	public $logger;

	/**
	 * @inject
	 * @var Service\Carousel
	 */
	public $carouselModel;

	public function actionDefault()
	{
		$carousel = $this->carouselModel->createNewCarousel();
		$this->redirect(':Private:Carousel:Edit:', array('id' => $carousel->id));
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