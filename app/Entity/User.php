<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

/**
 * @ORM\Entity
 * @Table(name="user")
 * @ORM\HasLifecycleCallbacks
 */
class User extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $identityNo;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $username;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $password;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $name;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $surname;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $email;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $role;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $status = 'ok';

	/**
	 * @var ArrayCollection
	 */
	protected $districts;

	public function __construct()
	{
		$this->districts = new ArrayCollection();
	}

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
	public function getIdentityNo()
	{
		return $this->identityNo;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getSurname()
	{
		return $this->surname;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getRole()
	{
		return $this->role;
	}

	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getDistricts()
	{
		return $this->districts;
	}



	/**
	 * ************************************* Setters ***************************************
	 */

	/**
	 * @param mixed $identityNo
	 */
	public function setIdentityNo($identityNo)
	{
		$this->identityNo = $identityNo;
		return $this;
	}

	public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}

	public function setPassword($password)
	{
		$this->password = self::calculateHash($password);
		return $this;
	}

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function setSurname($surname)
	{
		$this->surname = $surname;
		return $this;
	}

	public function setEmail($email)
	{
		$this->email = $email;
		return $this;
	}

	public function setRole($role)
	{
		$this->role = $role;
		return $this;
	}

	public function setStatus($status)
	{
		$this->status = $status;
		return $this;
	}

	public function destroy()
	{
		$this->status = 'del';
		return $this;
	}

	public function isOk()
	{
		return $this->status === 'ok' ? TRUE : FALSE;
	}

	public function isDel()
	{
		return $this->status === 'del' ? TRUE : FALSE;
	}

	/**
	 * Generate and set new Password to entity
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 * @return string
	 */
	public function generateNewPassword()
	{
		$specialChars = substr(str_shuffle('!"#$%&()*+,.-/:<=>?_'), 0, rand(1, 2));
		$password = str_shuffle(substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0,
				8 - strlen($specialChars)) . $specialChars);
		$this->setPassword($password);
		return $password;
	}

	/**
	 * Computes salted password hash.
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 * @param  string
	 * @return string
	 */
	public static function calculateHash($password, $salt = NULL)
	{
		if ($password === \Nette\Utils\Strings::upper($password)) { // perhaps caps lock is on
			$password = \Nette\Utils\Strings::lower($password);
		}
		return crypt($password, $salt ?: '$2a$07$jhiuzui' . \Nette\Utils\Random::generate(22));
	}

}

class InvalidUserException extends \Exception
{
}

;
