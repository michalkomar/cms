<?php

namespace App\PublicModule\Component;
use Doctrine\ORM\Query;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class FullPageImage extends \Nette\Application\UI\Control
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	public function __construct(\Kdyby\Doctrine\EntityManager $em)
	{
		$this->em = $em;
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var integer $textId
	 * @see Nette\Application\Control#render()
	 */
	public function render($params)
	{
		if (!isset($params['id'])) throw new \InvalidArgumentException('Image id is not set in database.');

		$this->getTemplate()->params = $params;
		$this->getTemplate()->image = $this->attachmentRepository()->find((int) $params['id']);
		$this->getTemplate()->setFile(__DIR__.'/templates/Image.latte');
		$this->getTemplate()->render();
	}

	public function attachmentRepository()
	{
		return $this->em->getRepository('\App\Entity\Attachment');
	}
}
