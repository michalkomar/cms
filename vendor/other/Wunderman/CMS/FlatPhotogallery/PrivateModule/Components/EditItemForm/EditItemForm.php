<?php

namespace App\PrivateModule\FlatPhotoGalleryModule\Component;
use App\Entity\FlatPhotoGalleryItem;
use App\PrivateModule\AttachmentModule\Model\Service\Attachment;
use App\PrivateModule\BoxesModule\Model\Service;
use App\PrivateModule\FlatPhotoGalleryModule\Model\Service\FlatPhotoGallery;
use Nette\Application\UI\Form;

/**
 * FlatPhotoGallery editItemForm
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class EditItemForm extends \Nette\Application\UI\Control
{
	/**
	 * @var Box
	 */
	public $gallery;

	/**
	 * @var integer
	 */
	public $itemId;

	/**
	 * @var \App\Entity\BoxItem
	 */
	private $item;

	/**
	 * @var Service\Boxes
	 */
	public $galleryModel;

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

	public function __construct(Attachment $attachmentService, FlatPhotoGallery $galleryModel, \App\Entity\FlatPhotoGallery $gallery, $editItemId = null)
	{
		$this->galleryModel = $galleryModel;
		$this->gallery = $gallery;
		$this->itemId = $editItemId;
		$this->attachmentService = $attachmentService;

		if(!is_null($editItemId))
		{
			$this->item = $this->galleryModel->readBoxItem($editItemId);
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

		if (is_null($this->gallery))
		{
			$this->getTemplate()->box = $this->gallery = new Box();
		}

		$this->getTemplate()->box = $this->gallery;
		$this->getTemplate()->item = $this->item = $this->galleryModel->readBoxItem($this->itemId);

		if (is_null($this->item))
		{
			$this->getTemplate()->item = new FlatPhotoGalleryItem();
		}
		else
		{
		}

		$this->getTemplate()->render();
	}

	public function setFormDefaults()
	{
		$this->itemForm->setDefaults([
			'title' => $this->item->title,
			'secondtitle' => $this->item->secondtitle,
			'color' => $this->item->color,
			'detailColor' => $this->item->detailColor,
			'text' => $this->item->text,
			'category' => $this->item->category
		]);
	}

	/**
	 * @return Form
	 */
	public function createComponentEditItemForm()
	{
		$this->itemForm = new Form();
		$this->itemForm->addText('title', 'Title');
		$this->itemForm->addText('secondtitle', 'Second title');
		$this->itemForm->addText('color', 'Color');
		$this->itemForm->addText('detailColor', 'Detail color');
		$this->itemForm->addTextArea('text', 'Detail text');
		$this->itemForm->addText('category', 'Category');
		$this->itemForm->addHidden('boxId', $this->gallery->id);
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
			$this->item = new BoxItem();
		}
		else
		{
			$this->item = $this->galleryModel->readBoxItem($values->item);
		}

		$this->item->setTitle($values->title)
			->setSecondtitle($values->secondtitle)
			->setText($values->text)
			->setColor(str_replace('#', '',$values->color))
			->setDetailColor(str_replace('#', '', $values->detailColor))
			->setCategory($values->category)
			->setBox($this->gallery);

		if ($values->image->isOk())
		{
			$imageId = $this->attachmentService->processFile('image');
			$this->item->setAttachment($this->galleryModel->em->getReference('\App\Entity\Attachment', $imageId));
		}

		$this->galleryModel->persist($this->item)->flush();
		$this->flashMessage('Item has saved.', 'success');

		$this->redirect('this');
	}

	/**
	 * @param $itemId
	 */
	public function handleDeleteItem($itemId)
	{
		$this->galleryModel->readBoxItem($itemId)->delete();
		$this->galleryModel->flush();
		$this->flashMessage('Item was deleted.', 'success');
	}

	public function handleDeleteImage($itemId)
	{
		$this->galleryModel->readBoxItem($itemId)->attachment = null;
		$this->galleryModel->flush();
	}
}
