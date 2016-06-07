<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;

/**
 * @ORM\Entity
 * @Table(name="company")
 * @ORM\HasLifecycleCallbacks
 */
class Company extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $staropramenId;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $customer = null;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $name;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $ico;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $phone;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $segment;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $status;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $meetingStatus;

	/**
	 * @ORM\OneToOne(targetEntity="User")
	 */
	protected $meetingStatusChangedBy = null;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $meetingStatusChanged;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $ropId;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $district;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $city;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $zip;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $street;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $streetNumberO;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $streetNumberP;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $mapDeleted = 0;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $description;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $lat;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $lng;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $tv = 0;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $xdsl = 0;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $fv = 0;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $pp = 0;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $blacklist = 0;

	/**
	 * @ORM\Column(type="string", name="o2_tv_accessibility")
	 */
	protected $o2TvAccessibility = 0;

	/**
	 * @ORM\Column(type="string", name="o2_tv_installed")
	 */
	protected $o2TvInstalled = null;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $isLead = 0;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $bscs = null;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $itemStatus = 'ok';

	/**
	 * @ORM\OneToOne(targetEntity="User")
	 */
	protected $deletedBy = null;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $deletedDatetime = null;


	/**
	 * ************************************* Getters ***************************************
	 */

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getStaropramenId()
	{
		return $this->staropramenId;
	}

	/**
	 * @return mixed
	 */
	public function getCustomer()
	{
		return $this->customer;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function getIco()
	{
		return $this->ico;
	}

	/**
	 * @return mixed
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * @return mixed
	 */
	public function getSegment()
	{
		return $this->segment;
	}

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return mixed
	 */
	public function getMeetingStatus()
	{
		return $this->meetingStatus;
	}

	/**
	 * @return mixed
	 */
	public function getMeetingStatusChangedBy()
	{
		return $this->meetingStatusChangedBy;
	}

	/**
	 * @return mixed
	 */
	public function getMeetingStatusChanged()
	{
		return $this->meetingStatusChanged;
	}

	/**
	 * @return mixed
	 */
	public function getRopId()
	{
		return $this->ropId;
	}

	/**
	 * @return mixed
	 */
	public function getDistrict()
	{
		return $this->district;
	}

	/**
	 * @return mixed
	 */
	public function getCity()
	{
		return $this->city;
	}

	/**
	 * @return mixed
	 */
	public function getZip()
	{
		return $this->zip;
	}

	/**
	 * @return mixed
	 */
	public function getStreet()
	{
		return $this->street;
	}

	/**
	 * @return mixed
	 */
	public function getStreetNumberO()
	{
		return $this->streetNumberO;
	}

	/**
	 * @return mixed
	 */
	public function getStreetNumberP()
	{
		return $this->streetNumberP;
	}

	/**
	 * @return mixed
	 */
	public function getMapDeleted()
	{
		return $this->mapDeleted;
	}

	/**
	 * @return mixed
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @return mixed
	 */
	public function getLat()
	{
		return $this->lat;
	}

	/**
	 * @return mixed
	 */
	public function getLng()
	{
		return $this->lng;
	}

	/**
	 * @return mixed
	 */
	public function getTv()
	{
		return $this->tv;
	}

	/**
	 * @return mixed
	 */
	public function getXdsl()
	{
		return $this->xdsl;
	}

	/**
	 * @return mixed
	 */
	public function getFv()
	{
		return $this->fv;
	}

	/**
	 * @return mixed
	 */
	public function getPp()
	{
		return $this->pp;
	}

	/**
	 * @return mixed
	 */
	public function getBlacklist()
	{
		return $this->blacklist;
	}

	/**
	 * @return mixed
	 */
	public function getO2TvAccessibility()
	{
		return $this->o2TvAccessibility;
	}

	/**
	 * @return mixed
	 */
	public function getO2TvInstalled()
	{
		return $this->o2TvInstalled;
	}

	/**
	 * @return mixed
	 */
	public function isLead()
	{
		return $this->isLead;
	}

	/**
	 * @return mixed
	 */
	public function getBscs()
	{
		return $this->bscs;
	}

	/**
	 * @return mixed
	 */
	public function getDeletedBy()
	{
		return $this->deletedBy;
	}

	/**
	 * @return mixed
	 */
	public function getDeletedDatetime()
	{
		return $this->deletedDatetime;
	}


	/**
	 * ************************************* Setters ***************************************
	 */

	/**
	 * @param mixed $staropramenId
	 */
	public function setStaropramenId($staropramenId)
	{
		$this->staropramenId = $staropramenId;

		return $this;
	}

	/**
	 * @param mixed $customer
	 */
	public function setCustomer($customer)
	{
		$this->customer = $customer;

		return $this;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @param mixed $ico
	 */
	public function setIco($ico)
	{
		$this->ico = $ico;

		return $this;
	}

	/**
	 * @param mixed $phone
	 */
	public function setPhone($phone)
	{
		$this->phone = $phone;

		return $this;
	}

	/**
	 * @param mixed $segment
	 */
	public function setSegment($segment)
	{
		$this->segment = $segment;

		return $this;
	}

	/**
	 * @param mixed $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * @param mixed $meetingStatus
	 */
	public function setMeetingStatus($meetingStatus)
	{
		$this->meetingStatus = $meetingStatus;

		return $this;
	}

	/**
	 * @param mixed $meetingStatusChangedBy
	 */
	public function setMeetingStatusChangedBy($meetingStatusChangedBy)
	{
		$this->meetingStatusChangedBy = $meetingStatusChangedBy;

		return $this;
	}

	/**
	 * @param mixed $meetingStatusChanged
	 */
	public function setMeetingStatusChanged($meetingStatusChanged)
	{
		$this->meetingStatusChanged = $meetingStatusChanged;

		return $this;
	}


	/**
	 * @param mixed $ropId
	 */
	public function setRopId($ropId)
	{
		$this->ropId = $ropId;

		return $this;
	}

	/**
	 * @param mixed $district
	 */
	public function setDistrict($district)
	{
		$this->district = $district;

		return $this;
	}

	/**
	 * @param mixed $city
	 */
	public function setCity($city)
	{
		$this->city = $city;

		return $this;
	}

	/**
	 * @param mixed $zip
	 */
	public function setZip($zip)
	{
		$this->zip = $zip;

		return $this;
	}

	/**
	 * @param mixed $street
	 */
	public function setStreet($street)
	{
		$this->street = $street;

		return $this;
	}

	/**
	 * @param mixed $streetNumberO
	 */
	public function setStreetNumberO($streetNumberO)
	{
		$this->streetNumberO = $streetNumberO;

		return $this;
	}

	/**
	 * @param mixed $streetNumberP
	 */
	public function setStreetNumberP($streetNumberP)
	{
		$this->streetNumberP = $streetNumberP;

		return $this;
	}

	/**
	 * @param mixed $mapDeleted
	 */
	public function setMapDeleted($mapDeleted)
	{
		$this->mapDeleted = $mapDeleted;

		return $this;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * @param mixed $lat
	 */
	public function setLat($lat)
	{
		$this->lat = $lat;

		return $this;
	}

	/**
	 * @param mixed $lng
	 */
	public function setLng($lng)
	{
		$this->lng = $lng;

		return $this;
	}

	/**
	 * @param mixed $tv
	 */
	public function setTv($tv)
	{
		$this->tv = $tv;

		return $this;
	}

	/**
	 * @param mixed $xdsl
	 */
	public function setXdsl($xdsl)
	{
		$this->xdsl = $xdsl;

		return $this;
	}

	/**
	 * @param mixed $fv
	 */
	public function setFv($fv)
	{
		$this->fv = $fv;

		return $this;
	}

	/**
	 * @param mixed $pp
	 */
	public function setPp($pp)
	{
		$this->pp = $pp;

		return $this;
	}

	/**
	 * @param mixed $blacklist
	 */
	public function setBlacklist($blacklist)
	{
		$this->blacklist = $blacklist;

		return $this;
	}

	/**
	 * @param mixed $o2TvAccessibility
	 */
	public function setO2TvAccessibility($o2TvAccessibility)
	{
		$this->o2TvAccessibility = $o2TvAccessibility;

		return $this;
	}

	/**
	 * @param mixed $o2TvInstalled
	 */
	public function setO2TvInstalled($o2TvInstalled)
	{
		$this->o2TvInstalled = $o2TvInstalled;

		return $this;
	}

	/**
	 * @param int|bool $isLead
	 */
	public function setAsLead($isLead)
	{
		$this->isLead = $isLead;

		return $this;
	}

	/**
	 * @param string $bscs
	 */
	public function setBscs($bscs)
	{
		$this->bscs = $bscs;

		return $this;
	}

	public function destroy()
	{
		$this->itemStatus = 'del';

		return $this;
	}

	/**
	 * @param mixed $deletedBy
	 */
	public function setDeletedBy($deletedBy)
	{
		$this->deletedBy = $deletedBy;

		return $this;
	}

	/**
	 * @param mixed $deletedDatetime
	 */
	public function setDeletedDatetime($deletedDatetime)
	{
		$this->deletedDatetime = $deletedDatetime;

		return $this;
	}
}

class NoCompanyException extends \Exception {};
