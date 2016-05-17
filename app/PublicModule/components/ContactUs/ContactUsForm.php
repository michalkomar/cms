<?php

namespace App\PublicModule\Component;
use App\ParametersFactory;
use App\service\Mailer;
use Doctrine\ORM\Query;
use Nette\Application\UI\Form as UIForm;
use Nette\Bridges\Framework\TracyBridge;
use Nette\Mail\IMailer;
use Tracy\Bridges\Nette\TracyExtension;
use Tracy\ILogger;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class ContactUsForm extends \Nette\Application\UI\Control
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
	 * @var ParametersFactory
	 */
	private $pf;

	/**
	 * Mail settings
	 * @var array
	 */
	private $ms;

	/**
	 * @param \Kdyby\Doctrine\EntityManager $em
	 * @param ILogger $logger
	 * @param IMailer $mailer
	 * @param $args
	 */
	public function __construct(\Kdyby\Doctrine\EntityManager $em, ILogger $logger, IMailer $mailer, $args, ParametersFactory $pf)
	{
		$this->em = $em;
		$this->logger = $logger;
		$this->mailer = $mailer;
		$this->pf = $pf;
		$this->ms = $this->pf->get('mailer');

		if (is_null($this->ms)) throw new \InvalidArgumentException('Missing mailer property in config parameters.');
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var integer $textId
	 * @see Nette\Application\Control#render()
	 */
	public function render()
	{
		$this->getTemplate()->setFile(__DIR__.'/templates/ContactUs.latte');

		if (!is_null($this->getPresenter()->getRequest()->getParameter('_fid')))
		{
			$this->getTemplate()->formSubmitted = true;
		}

		$this->getTemplate()->render();
	}

	/**
	 * @return UIForm
	 */
	public function createComponentContactForm()
	{
		$form = new UIForm();

		$form->addText('name', 'Name *')
			->setRequired('Fill out your name.')
			->addRule(UIForm::MAX_LENGTH, 'Name must have max %d letters.', 50);
		$form->addText('organisation', 'Organisation')
			->addRule(UIForm::MAX_LENGTH, 'Organisation name must have max %d letters.', 50);
		$form->addHidden('email');
		$form->addText('address', 'E-mail *')
			->addRule(UIForm::EMAIL, 'Fill out the e-mail in correct format.')
			->addRule(UIForm::MAX_LENGTH, 'Email must have max %d letters.', 150)
			->setRequired('Fill out email.');
		$form->addTextArea('message', 'Message *')
			->addRule(UIForm::MAX_LENGTH, 'Message must have max %d letters.', 1800)
			->setRequired('Enter your message.');

		$form->addText('phoneCode', 'Area code')
			->addRule(UIForm::MAX_LENGTH, 'Phone international code must have max %d letters.', 5);
		$form->addText('phone', 'Phone');

		$form->addCheckbox('consultation', 'Consultation');
		$form->addCheckbox('corporateLearning', 'Corporate learning');

//		$form->setAction($this->getPresenter()->link('this#form-ContactUs'));

		$form->addSubmit('send', 'send');

		$form->onValidate[] = [$this, 'validateForm'];
		$form->onSuccess[] = [$this, 'processForm'];

		return $form;
	}

	/**
	 * @param UIForm $form
	 *
	 * @return UIForm
	 */
	public function validateForm(UIForm $form)
	{
		$values = $form->getValues();

		if (!empty($values->email))
		{
			$form->addError('Trololo');
		}

		return true;
	}

	/**
	 * @param UIForm $form
	 */
	public function processForm(UIForm $form)
	{
		$values = $form->getValues(true);

		$this->createSenderMessage($values);
		$this->createRecipientMessage($values);

		$this->flashMessage('Form was successfully sended.');
		$this->redirect('this');
	}

	/**
	 * @param array $values
	 */
	public function createSenderMessage($values)
	{
		$this->mailer->setMailArgs(['values' => $values]);
		$this->mailer->setTemplatePath(realpath(__DIR__.'/templates/mail-sender.latte'));
		$message = $this->mailer->getMessage();
		$message->setHeader('To', $values['address']);
		$message->setSubject('Study Programs confirmation [no reply]');
		$this->mailer->send($message);
	}

	/**
	 * @param array $values
	 */
	public function createRecipientMessage($values)
	{
		$this->mailer->setMailArgs(['values' => $values]);
		$this->mailer->setTemplatePath(realpath(__DIR__.'/templates/mail-recipient.latte'));
		$message = $this->mailer->getMessage();

		if (!isset($this->ms['sender'])) throw new \InvalidArgumentException('Missing mailer.sender property in config parameters.');
		$message->setHeader('To', $this->ms['sender']);

		$subjectParts = [];
		if ($values['consultation']) $subjectParts[] = 'Consultation';
		if ($values['corporateLearning']) $subjectParts[] = 'Corporate learning';

		$message->setSubject(implode(', ',  $subjectParts). ' - contact form');
		$this->mailer->send($message);
	}

	/**
	 * @return \Kdyby\Doctrine\EntityRepository
	 */
	public function attachmentRepository()
	{
		return $this->em->getRepository('\App\Entity\Attachment');
	}
}
