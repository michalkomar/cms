<?php

namespace App\PrivateModule\FeedsModule\Component;
use App\Entity\Box;
use App\Entity\BoxItem;
use App\PrivateModule\AttachmentModule\Model\Service\Attachment;
use App\PrivateModule\FeedsModule\Model\Service;
use Nette\Application\UI\Form;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class EditItemForm extends \Nette\Application\UI\Control
{
	/**
	 * @var Box
	 */
	public $box;

	/**
	 * @var integer
	 */
	public $itemId;

	/**
	 * @var \App\Entity\BoxItem
	 */
	private $item;

	/**
	 * @var Service\Feeds
	 */
	public $feedsModel;

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

	public function __construct(Attachment $attachmentService, Service\Feeds $boxesModel, Box $box, $editItemId = null)
	{
		$this->feedsModel = $boxesModel;
		$this->box = $box;
		$this->itemId = $editItemId;
		$this->attachmentService = $attachmentService;

		if(!is_null($editItemId))
		{
			$this->item = $this->feedsModel->readBoxItem($editItemId);
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

		if (is_null($this->box))
		{
			$this->getTemplate()->box = $this->box = new Box();
		}

		$this->getTemplate()->box = $this->box;
		$this->getTemplate()->item = $this->item = $this->feedsModel->readBoxItem($this->itemId);

		if (is_null($this->item))
		{
			$this->getTemplate()->item = new BoxItem();
//			$this->setFormDefaults();
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
		$this->itemForm->addHidden('boxId', $this->box->id);
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
			$this->item = $this->feedsModel->readBoxItem($values->item);
		}

		$this->item->setTitle($values->title)
			->setSecondtitle($values->secondtitle)
			->setText($values->text)
			->setColor(str_replace('#', '',$values->color))
			->setDetailColor(str_replace('#', '', $values->detailColor))
			->setCategory($values->category)
			->setBox($this->box);

		if ($values->image->isOk())
		{
			$imageId = $this->attachmentService->processFile('image');
			$this->item->setAttachment($this->feedsModel->em->getReference('\App\Entity\Attachment', $imageId));
		}

		$this->feedsModel->persist($this->item)->flush();
		$this->flashMessage('Item has saved.', 'success');

		$this->redirect('this');
	}

	/**
	 * @param $itemId
	 */
	public function handleDeleteItem($itemId)
	{
		$this->feedsModel->readBoxItem($itemId)->delete();
		$this->feedsModel->flush();
		$this->flashMessage('Item was deleted.', 'success');
	}

	public function handleDeleteImage($itemId)
	{
		$this->feedsModel->readBoxItem($itemId)->attachment = null;
		$this->feedsModel->flush();
	}
}
