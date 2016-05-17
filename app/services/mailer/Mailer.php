<?php
	/**
	 * Created by PhpStorm.
	 * User: horacekp
	 * Date: 28/08/15
	 * Time: 13:57
	 */

	namespace App\service;

	use App\ParametersFactory;
	use Nette\Application\Application;
	use Nette\Application\IPresenter;
	use Nette\InvalidArgumentException;
	use Nette\Mail\IMailer;
	use Nette\Mail\Message;
	use Tracy\ILogger;
	use Nette\Bridges\ApplicationLatte\UIMacros;

	class Mailer extends \Nette\Object implements IMailer
	{

		/**
		 * @var \PHPMailer
		 */
		private $mailer;

		/**
		 * @var \App\ParametersFactory
		 */
		private $config;

		/**
		 * @var ILogger
		 */
		private $logger;

		/**
		 * @type IPresenter
		 */
		private $presenter;

		/**
		 * @var \Nette\Mail\Message
		 */
		private $message;

		/**
		 * @var array
		 */
		private $mailerConfig;

		/**
		 * @var string|array
		 */
		private $templatePath;

		/**
		 * @var array
		 */
		private $mailArgs;

		/**
		 * @var \Nette\Application\Application
		 */
		private $application;

		/**
		 * @const array
		 */
		private $requiredMailerSettings = ['from', 'fromName', 'sender', 'domain', 'host', 'username', 'password'];


		public function __construct(ParametersFactory $config, ILogger $logger, Application $application)
		{
			$this->config = $config;
			$this->logger = $logger;
			$this->application = $application;
			$this->mailerConfig = $this->config->get('mailer');
			$this->checkConfig();

			$this->prepareMailer();

		}

		private function prepareMailer()
		{
			$this->mailer = new \PHPMailer(true);
			$this->mailer->isHTML();
			$this->mailer->Mailer = 'smtp';
			$this->mailer->CharSet = 'utf-8';
			$this->mailer->Encoding = 'base64';
			$this->mailer->clearCustomHeaders();

			$this->mailer->Host     = $this->mailerConfig['host'];
			$this->mailer->Mailer   = "smtp";
			$this->mailer->SMTPAuth = true;
			$this->mailer->Username = $this->mailerConfig['username'];
			$this->mailer->Password = $this->mailerConfig['password'];
		}

		/**
		 * @param string $name
		 * @param siring $subject
		 * @param string|array $to
		 * @param array $args
		 * @param null|string|array $bcc
		 */
		public function sendMail($subject, $to, array $args = array(), $bcc = null)
		{
			$this->presenter = $this->application->getPresenter();
			$this->mailArgs = $args;

			$this->mailer->Subject = $subject;
			$this->mailer->addReplyTo($this->mailerConfig['from']);
			$this->mailer->Sender = $this->mailerConfig['from'];
			$this->mailer->From = $this->mailerConfig['from'];
			$this->mailer->FromName = $this->mailerConfig['fromName'];
			$this->mailer->addCustomHeader("List-Unsubscribe",'<mailto:abuse@' . $this->mailerConfig['domain'] . '?subject=List-Unsubscribe&body=List-Unsubscribe:' . $to . '>');
			$this->mailer->ClearAllRecipients();
			$this->mailer->AddAddress($to);
			$this->mailer->DKIM_domain = $this->mailerConfig['DKIM_domain'];
			$this->mailer->DKIM_private = realpath($this->mailerConfig['DKIM_private']);
			$this->mailer->DKIM_selector = $this->mailerConfig['DKIM_selector'];
			if (is_string($bcc)) $this->mailer->addBCC($bcc);
			$this->mailer->Body = $this->createMessageTemplate();


			try
			{
				$this->mailer->send();
			}
			catch (\Exception $e)
			{
				$this->logger->log($e);
				throw $e;
			}
		}

		/**
		 * @param Message $message
		 *
		 * @throws \Exception
		 */
		public function send(Message $message)
		{
			if (!is_array($this->mailArgs))
			{
				throw new InvalidArgumentException('Mail args expected array ' . gettype($this->mailArgs) . ' given.');
			}

			$this->sendMail($message->getSubject(), $message->getHeader('To'), $this->mailArgs, $message->getHeader('Bcc'));
		}

		/**
		 * @return \Nette\Mail\Message
		 */
		public function createMessage()
		{
			$this->message = new \Nette\Mail\Message();

			$this->message
				->setFrom($this->mailerConfig['from'])
				->setHtmlBody($this->createMessageTemplate());

			return $this->message;
		}

		/**
		 * @return Message
		 */
		public function getMessage()
		{
			if (is_null($this->message))
			{
				$this->createMessage();
			}

			return $this->message;
		}

		/**
		 * @return string
		 * @throws \Exception
		 * @throws \Nette\Utils\JsonException
		 */
		public function createMessageTemplate()
		{
			if (!is_array($this->mailArgs))
			{
				throw new InvalidArgumentException('Mail args expected array ' . gettype($this->mailArgs) . ' given.');
			}
			if (is_null($this->templatePath))
			{
				throw new InvalidArgumentException('Mail name expected string ' . gettype($this->templatePath) . ' given.');
			}

			$template = new \Latte\Engine();

			$params = array(
				'_presenter' => $this->presenter,
				'_control' => $this->presenter,
				'basePath' => '',
				'templatePath' => $this->templatePath
			);
			$params += $this->mailArgs;

			UIMacros::install($template->getCompiler());

			$ds = DIRECTORY_SEPARATOR;

			return $template->renderToString(realpath(__DIR__).$ds.'@layout.latte', $params);
		}

		/**
		 * @param array $args
		 */
		public function setMailArgs(Array $args)
		{
			$this->mailArgs = $args;
		}

		/**
		 * @param string $path
		 */
		public function setTemplatePath($path)
		{
			$this->templatePath = $path;
		}

		public function checkConfig()
		{
			if (is_null($this->mailerConfig)) throw new InvalidArgumentException('Missing mailer property in config parameters.');

			foreach ($this->requiredMailerSettings as $settingKey)
			{
				if (is_null(\Nette\Utils\Arrays::get($this->mailerConfig, $settingKey, null)))
				{
					$this->logger->log('Missing mailer.'.$settingKey. ' property in config parameters required in '. get_class($this));
				}
			}

		}
	}
