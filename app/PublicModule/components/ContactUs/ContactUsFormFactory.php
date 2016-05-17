<?php

namespace App\PublicModule\Component;
use App\ParametersFactory;
use App\service\Mailer;
use Doctrine\ORM\Query;
use Nette\Mail\IMailer;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class ContactUsFormFactory extends \Nette\Application\UI\Control
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/**
	 * @var Mailer
	 */
	private $mailer;

	/**
	 * @var \Tracy\ILogger
	 */
	private $logger;

	/**
	 * @type ParametersFactory
	 */
	private $pf;

	/**
	 * @param \Kdyby\Doctrine\EntityManager $em
	 * @param \Tracy\ILogger $logger
	 * @param Mailer $mailer
	 */
	public function __construct(\Kdyby\Doctrine\EntityManager $em, \Tracy\ILogger $logger, IMailer $mailer, ParametersFactory $pf)
	{
		$this->em = $em;
		$this->logger = $logger;
		$this->mailer = $mailer;
		$this->pf = $pf;
	}

	/**
	 * @param $args
	 *
	 * @return ContactUsForm
	 */
	public function create($args)
	{
		return new \App\PublicModule\Component\ContactUsForm($this->em, $this->logger, $this->mailer, $args, $this->pf);
	}
}
