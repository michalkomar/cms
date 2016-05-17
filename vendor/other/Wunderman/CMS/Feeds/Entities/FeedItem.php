<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Entity
 * @Table(name="feed_item")
 * @ORM\HasLifecycleCallbacks
 */
class FeedItem extends \Kdyby\Doctrine\Entities\BaseEntity
{

	public function __construct($type, $datetime, $header, $perex, $detail, $feedItemId)
	{
		$this->type = $type;
		$this->datetime = $datetime;
		$this->header = $header;
		$this->perex = $perex;
		$this->detail = $detail;
		$this->feedItemId = $feedItemId;
		$this->isDisplay = TRUE;
	}

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", options={"unsigned"=true})
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=16, nullable=false)
	 */
	protected $type;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $datetime;

	/**
	 * @ORM\Column(type="string", length=255, name="`header`")
	 */
	protected $header;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $perex;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 */
	protected $detail;

	/**
	 * @ORM\Column(type="string", name="feed_item_id", columnDefinition="CHAR(32) NOT NULL", nullable=false)
	 */
	protected $feedItemId;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 */
	protected $status = 'ok';

	/**
	 * @ORM\Column(type="boolean", name="is_display", nullable=false, options={"default"=true})
	 */
	protected $isDisplay;


	/**
	 * ************************************* Setters ***************************************
	 */


	/**
	 * ************************************* Getters ***************************************
	 */

	public function getId()
	{
		return $this->id;
	}

	public function getItems($status = 'ok')
	{
		$criteria = Criteria::create();
		$criteria->where(Criteria::expr()->eq('status', $status))->orderBy(array('position' => 'ASC'));

		return $this->items->matching($criteria);
	}

	public function destroy()
	{
		$this->status = 'del';
	}
}