<?php

namespace App\PublicModule\Component;
use Doctrine\ORM\Query;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class YoutubeVideo extends \Nette\Application\UI\Control
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
		$this->getTemplate()->params = $params;
		$this->getTemplate()->image = isset($params['id']) ? $this->attachmentRepository()->find((int) $params['id']) : FALSE;
		$this->getTemplate()->setFile(__DIR__.'/templates/YoutubeVideo.latte');
		$this->getTemplate()->render();
	}

	/**
	 * @return \Kdyby\Doctrine\EntityRepository
	 */
	public function attachmentRepository()
	{
		return $this->em->getRepository('\App\Entity\Attachment');
	}
}
