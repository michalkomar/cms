<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

/**
 * @ORM\Entity
 * @Table(name="user_activity")
 * @ORM\HasLifecycleCallbacks
 */
class UserActivity extends \Kdyby\Doctrine\Entities\BaseEntity
{

	public function __construct(
		\Nette\Security\User $user,
		\Kdyby\Doctrine\EntityManager $em,
		\Nette\Http\Request $request
	) {
		$userRepository = $em->getRepository('\App\Entity\User');

		$this->setIp($request->getRemoteAddress());
		$this->setDatetime(new \DateTime());
		$this->setPost(\Nette\Utils\Json::encode($request->getPost()));
		$this->setGet($_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);

		if ($user->isLoggedIn()) {
			$this->setUser($userRepository->find($user->getId()));
		}
	}

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\OneToOne(targetEntity="User")
	 */
	protected $user;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $ip;

	/**
	 * @ORM\Column(type="datetime", name="`datetime`")
	 */
	protected $datetime;

	/**
	 * @ORM\Column(type="string", name="`get`")
	 */
	protected $get;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $post;

	/**
	 * ************************************* Getters ***************************************
	 */

	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return User|null
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @return mixed
	 */
	public function getIp()
	{
		return $this->ip;
	}

	/**
	 * @return mixed
	 */
	public function getDatetime()
	{
		return $this->datetime;
	}

	/**
	 * @return mixed
	 */
	public function getGet()
	{
		return $this->get;
	}

	/**
	 * @return mixed
	 */
	public function getPost()
	{
		return $this->post;
	}

	/**
	 * ************************************* Setters ***************************************
	 */

	/**
	 * @param User|null $user
	 * @return $this
	 */
	public function setUser(User $user = NULL)
	{
		$this->user = $user;

		return $this;
	}

	public function setIp($ip)
	{
		$this->ip = $ip;

		return $this;
	}

	/**
	 * @param \DateTime|NULL $datetime
	 * @return $this
	 */
	public function setDatetime(\DateTime $datetime = NULL)
	{
		$this->datetime = $datetime;

		return $this;
	}

	public function setGet($get)
	{
		$this->get = $get;

		return $this;
	}

	public function setPost($post)
	{
		$this->post = $post;

		return $this;
	}

}
