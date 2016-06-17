<?php

namespace App\PrivateModule\Component;
use App\PrivateModule\AttachmentModule\Model\Service\AttachmentService;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class Wysiwyg extends \Nette\Application\UI\Control
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/**
	 * @type AttachmentService
	 */
	private $attachmentService;

	/**
	 * Wysiwyg constructor.
	 *
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param AttachmentService $attachmentService
	 */
	public function __construct(\Doctrine\ORM\EntityManager $em, AttachmentService $attachmentService)
	{
		$this->em = $em;
		$this->attachmentService = $attachmentService;
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var integer $menuId
	 * @see Nette\Application\Control#render()
	 */
	public function render()
	{
		$this->getTemplate()->setFile(__DIR__.'/templates/Wysiwyg.latte');
		$this->getTemplate()->render();
	}

	public function handleReadImages()
	{
		$images = $this->attachmentRepository()->createQueryBuilder('i')
				->select('i')
				->where('i.status = :statusOk')
				->setParameter('statusOk', 'ok')
				->getQuery()->getArrayResult();

		array_walk($images, function(&$item){
			$item['name'] = $item['md5'];
			$item['type'] = 'f';
		});

		$this->getPresenter()->sendJson($images);
	}

	public function handleDestroyImage()
	{
		$md5 = $this->getPresenter()->getRequest()->getPost('name');
		$attachment = $this->attachmentRepository()->findOneBy(['md5' => $md5])->destroy();
		$this->em->flush();
	}

	public function handleUploadImage()
	{
		$id = $this->attachmentService->processFile('file');
		$file = $this->attachmentRepository()->find($id);

		$this->getPresenter()->sendJson([
			'type' => 'f',
			'name' => $file->getMd5(),
			'size' => $file->getSize()
		]);
	}

	public function attachmentRepository()
	{
		return $this->em->getRepository('\App\Entity\Attachment');
	}
}
