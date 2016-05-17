<?php

namespace App\PublicModule\ComposeModule\Component;
use Nette\InvalidArgumentException;
use Nette\Utils\Strings;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class ComponentWrapper extends \Nette\Application\UI\Control
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/**
	 * @var array
	 */
	private $pageAttributes = [];

	/**
	 * @inject
	 * @var \Tracy\ILogger
	 */
	public $logger;

	protected $allowedComponentTypes = [];

	public function __construct(\Kdyby\Doctrine\EntityManager $em, $itemId, $params, $logger, $allowedComponentsTypes)
	{
		$this->em = $em;
		$this->pageAttributes = $params;
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
		if (($component = \Nette\Utils\Arrays::get($pageAttributes, 'type', null)) === null)
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

	/**
	 * @param $name
	 *
	 * @return mixed
	 */
	public function createComponent($name)
	{
		$className = '\App\PublicModule\ComposeModule\Component\\'.ucfirst($name);

		if (! class_exists($className))
		{
			throw new \InvalidArgumentException("Trida komponenty {$className} nebyla nalezena. Zaregistrovali jste rozšíření v config.neon?");
		}
		else
		{
			$component = new $className($this->em, $this->pageAttributes, $this->logger);
		}

		return $component;
	}
}
