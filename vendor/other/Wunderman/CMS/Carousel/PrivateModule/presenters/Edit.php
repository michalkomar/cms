<?php

namespace App\PrivateModule\CarouselModule\Presenter;
use App\PrivateModule\CarouselModule\Component\EditItemForm;
use App\PrivateModule\CarouselModule\Model\Service;
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
	 * @var Service\Carousel
	 */
	public $carouselModel;

	/**
	 * @var \App\Entity\Carousel
	 */
	public $carousel;

	/**
	 * @var \App\Entity\CarouselItem
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
	public function actionDefault($id, $editItemId = null, $do = null)
	{
		$this->getTemplate()->carousel = $this->carousel = $this->carouselModel->readCarousel($id);
	}

	/**
	 * @return Form
	 */
	public function createComponentBoxPreferencesForm()
	{
		$form = new Form();
		$form->addSubmit('save', 'Save');
		$form->addText('name', 'Name')->setDefaultValue($this->carousel->name === 'newBox' ?: $this->carousel->name);
		$form->addCheckbox('showNavigation', 'Show navigation')->setDefaultValue($this->carousel->showNavigation);
		$form->addCheckbox('showHeader', 'Show navigation')->setDefaultValue($this->carousel->showHeader);

		$form->onSuccess[] = array($this, 'editPreferences');

		return $form;
	}

	/**
	 * @param Form $form
	 */
	public function editPreferences(Form $form)
	{
		$values = $form->getValues();
		$this->carousel->setName($values->name)->setShowNavigation($values->showNavigation)->setShowHeader($values->showHeader);
		$this->em->flush();

		$this->flashMessage('Carousel was saved.', 'success');

		if ($this->isAjax())
		{
			$this->redrawControl('carouselEditForm');
			$this->redrawControl('flashes');
		}
		else
		{
			$this->redirect('this');
		}
	}

	public function handleEditCarouselItem()
	{
		$this->item = $this->carouselModel->readCarouselItem($this->getParameter('editItemId'));
	}

	public function handleMoveRight()
	{
		$item = $this->carouselModel->readCarouselItem($this->getParameter('moveItemId'));
		$item->setPosition($item->position +1);
		$this->em->flush();

		if ($this->isAjax())
		{
			$this->redrawControl('editFormItem');
		}
	}

	public function handleMoveLeft()
	{
		$item = $this->carouselModel->readCarouselItem($this->getParameter('moveItemId'));
		$item->setPosition($item->position -1);
		$this->em->flush();

		if ($this->isAjax())
		{
			$this->redrawControl('editFormItem');
		}
	}

	public function createComponentEditItem()
	{
		return new EditItemForm($this->attachmentService, $this->carouselModel, $this->carousel, $this->getParameter('editItemId'));
	}

	public function createComponentNewItem()
	{
		return new EditItemForm($this->attachmentService, $this->carouselModel, $this->carousel);
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