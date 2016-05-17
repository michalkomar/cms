<?php

namespace Wunderman\CMS\PrivateModule\PagesModule\Component;
use Kdyby\Doctrine\EntityManager;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class Text extends \App\PublicModule\Component\Text
{
	/**
	 * @var array
	 */
	protected $componentParams;

	public function __construct(EntityManager $em, $componentParams)
	{
		parent::__construct($em);

		$this->componentParams = $componentParams;
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var integer $id - not used, only api compatibility
	 * @see Nette\Application\Control#render()
	 */
	public function render($id = null)
	{
		$params = [];
		foreach ($this->componentParams->params as $param)
		{
			$params[$param->name] = $param->value;
		}

		parent::render($params);
	}
}
