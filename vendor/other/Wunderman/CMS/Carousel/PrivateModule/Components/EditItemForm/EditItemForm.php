<?php

namespace App\PrivateModule\CarouselModule\Component;
use App\Entity\Carousel;
use App\Entity\CarouselItem;
use App\PrivateModule\AttachmentModule\Model\Service\Attachment;
use App\PrivateModule\CarouselModule\Model\Service;
use Nette\Application\UI\Form;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class EditItemForm extends \Nette\Application\UI\Control
{
	/**
	 * @var Carousel
	 */
	private $carousel;

	/**
	 * @var integer
	 */
	public $itemId;

	/**
	 * @var \App\Entity\CarouselItem
	 */
	private $item;

	/**
	 * @var Service\Carousel
	 */
	private $carouselModel;

	/**
	 * @var \Nette\Application\UI\Form
	 */
	private $itemForm;

	/**
	 * @var Attachment
	 */
	private $attachmentService;

	/**
	 * @var array
	 */
	private $itemFormsDefaults = [];

	public function __construct(Attachment $attachmentService, Service\Carousel $carouselModel, Carousel $carousel, $editItemId = null)
	{
		$this->carouselModel = $carouselModel;
		$this->carousel = $carousel;
		$this->itemId = $editItemId;
		$this->attachmentService = $attachmentService;

		if(!is_null($editItemId))
		{
			$this->item = $this->carouselModel->readCarouselItem($editItemId);
		}
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var integer $menuId
	 * @see Nette\Application\Control#render()
	 */
	public function render()
	{
		$this->getTemplate()->setFile(__DIR__.'/templates/EditItemForm.latte');

		if (is_null($this->carousel))
		{
			$this->getTemplate()->box = $this->carousel = new Carousel();
		}

		$this->getTemplate()->carousel = $this->carousel;
		$this->getTemplate()->item = $this->item = $this->carouselModel->readCarouselItem($this->itemId);

		if (is_null($this->item))
		{
			$this->getTemplate()->item = new CarouselItem();
		}
		else
		{
		}

		$this->getTemplate()->render();
	}

	public function setFormDefaults()
	{
		$this->itemForm->setDefaults([
			'text' => $this->item->text,
			'text2' => $this->item->text2,
		]);
	}

	/**
	 * @return Form
	 */
	public function createComponentEditItemForm()
	{
		$this->itemForm = new Form();
		$this->itemForm->addTextArea('text', 'Text');
		$this->itemForm->addTextArea('text2', 'Second text');
		$this->itemForm->addHidden('carouselId', $this->carousel->id);
		$this->itemForm->addHidden('item', $this->itemId);
		$this->itemForm->addHidden('requestKey', $this->getPresenter()->storeRequest('+ 20 minutes'));
		$this->itemForm->addUpload('image', 'Image')
				->addCondition(Form::FILLED)
				->addRule(Form::IMAGE, 'Image must be JPEG, PNG or GIF.');

		$this->itemForm->addSubmit('save', 'Save');

		if (!is_null($this->item))
		{
			$this->setFormDefaults();
		}

		$this->itemForm->onSuccess[] = array($this, 'saveItem');

		return $this->itemForm;
	}

	/**
	 * @param Form $form
	 *
	 * @throws \Doctrine\ORM\ORMException
	 */
	public function saveItem(Form $form)
	{
		$values = $form->getValues();

		if (!$values->item)
		{
			$this->item = new CarouselItem();
		}
		else
		{
			$this->item = $this->carouselModel->readCarouselItem($values->item);
		}

		$this->item
			->setText($values->text)
			->setText2($values->text2)
			->setCarousel($this->carousel);

		if ($values->image->isOk())
		{
			$imageId = $this->attachmentService->processFile('image');
			$this->item->setAttachment($this->carouselModel->em->getReference('\App\Entity\Attachment', $imageId));
		}

		$this->carouselModel->persist($this->item)->flush();
		$this->flashMessage('Item has saved.', 'success');

		$this->redirect('this');
	}

	/**
	 * @param $itemId
	 */
	public function handleDeleteItem($itemId)
	{
		$this->carouselModel->readCarouselItem($itemId)->delete();
		$this->carouselModel->flush();
		$this->flashMessage('Item was deleted.', 'success');
	}

	public function handleDeleteImage($itemId)
	{
		$this->carouselModel->readCarouselItem($itemId)->attachment = null;
		$this->carouselModel->flush();
	}
}
