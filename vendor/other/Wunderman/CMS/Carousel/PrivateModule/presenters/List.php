<?php

	namespace App\PrivateModule\CarouselModule\Presenter;
	use App\PrivateModule\CarouselModule\Model\Service;

class ListPresenter extends \App\PrivateModule\PrivatePresenter
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

	public function renderDefault()
	{
		$this->getTemplate()->carousels = $this->carouselModel->readCarousels();
	}

	public function handleRemoveItem($item)
	{
		try {
			$this->carouselModel->destroyCarousel($item);
		    $this->flashMessage('Carousel has been deleted.', 'success');
		} catch (\Exception $e) {
		    $this->logger->log($e);
			$this->flashMessage('Carousel cannot be deleted. Error was logged.', 'danger');
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