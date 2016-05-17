<?php

namespace App\PrivateModule\PagesModule\Component;
use App\Entity\ComposeArticleItem;
use Nette\InvalidArgumentException;
use Nette\Utils\Strings;
use Tracy\ILogger;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class ComponentWrapper extends \App\PublicModule\ComposeModule\Component\ComponentWrapper
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/**
	 * @var ILogger
	 */
	public $logger;

	private $pageAttributes;

	/**
	 * @var array
	 */
	private $allowedComponentsTypes;

	public function __construct(\Kdyby\Doctrine\EntityManager $em, ILogger $logger, $allowedComponentsTypes)
	{
		$this->em = $em;
		$this->logger = $logger;
		$this->allowedComponentTypes = $allowedComponentsTypes;
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var integer $textId
	 * @see Nette\Application\Control#render()
	 */
	public function render($pageAttributes)
	{
		$this->pageAttributes = $pageAttributes;
		if (($component = $pageAttributes->type) === null)
		{
			throw new InvalidArgumentException('Polozka stranky nema nastaveny typ.');
		}
		if (! array_key_exists($component, $this->allowedComponentTypes))
		{
			throw new InvalidArgumentException('Polozka stranky ma nastaveny nepovoleny typ "'.$component.'", povoleny jsou typy '.implode(', ', array_keys($this->allowedComponentTypes)));
		}

		$this->getTemplate()->pageAttributes = $pageAttributes;
		$this->getTemplate()->setFile(__DIR__.'/templates/ComponentWrapper.latte');
		$this->getTemplate()->render();
	}

	public function createComponent($name)
	{
		$className = '\Wunderman\CMS\PrivateModule\PagesModule\Component\\'.ucfirst($name);
		$component = new $className($this->em, $this->pageAttributes, $this->logger);

		return $component;
	}
}
