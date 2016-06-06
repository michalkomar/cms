<?php

namespace App\PublicModule\Component;

use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityManager;

class FullPageImage extends \Nette\Application\UI\Control
{

	/**
	 * @var EntityManager
	 */
	private $em;


	public function __construct(\Kdyby\Doctrine\EntityManager $em)
	{
		$this->em = $em;
	}


	/**
	 * @param  array  $params
	 * @return void
	 */
	public function render(array $params)
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
