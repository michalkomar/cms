<?php

namespace App\PrivateModule\AttachmentModule\Model\Service;
use App\ParametersFactory;
use Tracy\ILogger;

/**
 * Task service
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class Attachment extends \Nette\Object
{

	/** @var \Kdyby\Doctrine\EntityManager $entityManager */
	private $entityManager;

	/** @var \Nette\Http\Request $httpRequest */
	private $httpRequest;

	/** @var \App\Entity\Attachment $attachmentEntity */
	private $attachmentEntity;

	/** @var string $attachmentDir */
	private $attachmentDir;

	/**
	 * @type ILogger
	 */
	private $logger;

	/**
	 * @param \Kdyby\Doctrine\EntityManager $entityManager
	 * @param \Nette\Http\Request $httpRequest
	 * @param \Nette\DI\Container $context
	 */
	public function __construct(\Kdyby\Doctrine\EntityManager $entityManager, \Nette\Http\Request $httpRequest, \Nette\DI\Container $context, ParametersFactory $config, ILogger $logger)
	{
		$this->entityManager = $entityManager;
		$this->httpRequest = $httpRequest;
		$this->logger = $logger;

		$attachmentDir = $config->get('attachmentsDir');

		if (!is_dir($attachmentDir)) {
			mkdir($attachmentDir, 0777, TRUE);
		}

		$this->attachmentDir = realpath($attachmentDir);
	}

	/**
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 * @param string $name File name in http request or \Nette\HttpRequest\FileUpload
	 * @return int Attachment id
	 * @throws Exception
	 */
	public function processFile($name)
	{
		if (!is_string($name) && get_class($name) == "Nette\Http\FileUpload")
		{
			$file = $name;
		}
		else
		{
			$file = $this->httpRequest->getFile($name);
		}
		$tmpFile = $file->getTemporaryFile();
		$fileMD5 = md5_file($tmpFile);

		try
		{
			$file->move($this->attachmentDir . DIRECTORY_SEPARATOR . $fileMD5);

			$this->attachmentEntity = $this->attachmentRepository()->findOneBy(array('md5' => $fileMD5));

			if (empty($this->attachmentEntity))
			{
				$this->attachmentEntity = new \App\Entity\Attachment();

				$this->attachmentEntity->setMd5($fileMD5)
							->setSize($file->getSize())
							->setType($file->getContentType());

				$this->entityManager->persist($this->attachmentEntity)->flush();
			}
			else
			{
				$this->attachmentEntity->setOk();
			}

			return $this->attachmentEntity->getId();
		}
		catch ( \Exception $e )
		{
			$this->logger->log($e);
		}
	}

	// <editor-fold defaultstate="collapsed" desc="Getters">

	/**
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 * @return \Kdyby\Doctrine\EntityDao
	 */
	public function attachmentRepository()
	{
		return $this->entityManager->getRepository('\App\Entity\Attachment');
	}
	// </editor-fold>
}
