<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Nette\InvalidArgumentException;

/**
 * @ORM\Entity
 * @Table(name="email")
 * @ORM\HasLifecycleCallbacks
 */
class Email extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\OneToOne(targetEntity="User")
	 */
	protected $sender;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $recipient;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $template;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $subject;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $params;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $created;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $sended;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $error;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $status = 'new';

	/**
	 * ************************************* Getters ***************************************
	 */

	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getSender()
	{
		return $this->sender;
	}

	/**
	 * @return mixed
	 */
	public function getRecipient()
	{
		return $this->recipient;
	}

	/**
	 * @return mixed
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * @return mixed
	 */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * @return mixed
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @return mixed
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @return mixed
	 */
	public function getSended()
	{
		return $this->sended;
	}

	/**
	 * @return mixed
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return $this->status;
	}


	/**
	 * ************************************* Setters ***************************************
	 */

	/**
	 * @param \App\Entity\User $sender
	 * @return $this
	 */
	public function setSender(\App\Entity\User $sender = null)
	{
		$this->sender = $sender;

		return $this;
	}

	/**
	 * @param string $recipient
	 * @return $this
	 */
	public function setRecipient($recipient)
	{
		if (!is_string($recipient) && !is_array($recipient)) throw new InvalidArgumentException('Recipient name must be a string or array.');

		if (is_array($recipient))
		{
			$this->recipient = implode(',', $recipient);
		}
		else
		{
			$this->recipient = $recipient;
		}

		return $this;
	}

	/**
	 * @param string $template
	 * @return $this
	 */
	public function setTemplate($template)
	{
		if (!is_string($template)) throw new InvalidArgumentException('Template name must be a string.');

		$this->template = $template;

		return $this;
	}

	/**
	 * @param string $subject
	 * @return $this
	 */
	public function setSubject($subject)
	{
		if (!is_string($subject)) throw new InvalidArgumentException('Subject must be a string.');

		$this->subject = $subject;

		return $this;
	}

	/**
	 * @param array $params
	 * @return $this
	 */
	public function setParams(array $params)
	{
		$this->params = \Nette\Utils\Json::encode($params);

		return $this;
	}

	/**
	 * @param string $error
	 * @return $this
	 */
	public function setError($error)
	{
		if (!is_string($error)) throw new InvalidArgumentException('Sending error must be a string.');

		$this->status = 'postponed';
		$this->error = $error;

		return $this;
	}

	/**
	 * Set email status to sended
	 * @return $this
	 */
	public function setSended()
	{
		$this->sended = new \DateTime();
		$this->status = 'sended';
		$this->error = null;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSended()
	{
		return $this->status === 'sended' ? true : false;
	}

	/**
	 * ************************************* Events ***************************************
	 */

	/**
	 * @ORM\PrePersist
	 */
	public function onPrePersistSetCreateDate()
	{
		$this->created = new \DateTime();
	}

}
