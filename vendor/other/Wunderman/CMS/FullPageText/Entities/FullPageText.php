<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Petr Besir Horáček <sirbesir@gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="full_page_text")
 */
class FullPageText extends \Kdyby\Doctrine\Entities\BaseEntity
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
    protected $linkText;

    /**
     * @ORM\Column(type="string")
     */
    protected $link;

    /**
     * @ORM\Column(type="string")
     */
    protected $bgColor;

    /**
     * @ORM\Column(type="string")
     */
    protected $content;

    /**
     * @ORM\Column(type="enum")
     */
    protected $status = 'ok';

}
