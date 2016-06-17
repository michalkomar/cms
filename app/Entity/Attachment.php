<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

/**
 * @ORM\Entity
 * @Table(name="attachment")
 * @ORM\HasLifecycleCallbacks
 */
class Attachment extends \Kdyby\Doctrine\Entities\BaseEntity
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
	protected $md5;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $size;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $type;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $status = 'ok';

	/**
	 * ************************************* Setters ***************************************
	 */

	public function setMd5($md5)
	{
		$this->md5 = $md5;
		return $this;
	}

	public function setSize($size)
	{
		$this->size = $size;
		return $this;
	}

	public function destroy()
	{
		$this->status = 'del';
	}

	public function setOk()
	{
		$this->status = 'ok';
	}

	/**
	 * ************************************* Getters ***************************************
	 */

	public function getId()
	{
		return $this->id;
	}

	public function getMd5()
	{
		return $this->md5;
	}

	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @return mixed
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * ************************************* Hydration ***************************************
	 */

	/**
	 * Get entity properties as array
	 * @return array
	 */
	public function toArray()
	{
		$result = [];

		foreach (get_object_vars($this) as $key => $value) {
			$result[$key] = $value;
		}
		return $result;
	}

}
