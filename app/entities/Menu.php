<?php

namespace App\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Petr Besir Horáček <sirbesir@gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="menu")
 */
class Menu extends \Kdyby\Doctrine\Entities\BaseEntity
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
    protected $name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $published;

    /**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="menu")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $items;

    /**
     * @ORM\Column(type="string")
     */
    protected $status = 'ok';

    /**
     * @param $status 'ok' or 'del'
     *
     * @return mixed
     */
    public function getItems($status = 'ok')
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('status', $status))->orderBy(['lft'=>'ASC']);

        return $this->items->matching($criteria);
    }

}
