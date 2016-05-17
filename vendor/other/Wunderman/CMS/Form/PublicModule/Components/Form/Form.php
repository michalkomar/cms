<?php

namespace App\PublicModule\Component;
use Doctrine\ORM\Query;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class Form extends \Nette\Application\UI\Control
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/**
	 * @var \Doctrine\ORM\PersistentCollection
	 */
	private $componentParams;

	/**
	 * @inject
	 * @var \Tracy\ILogger
	 */
	public $logger;

	public function __construct(\Kdyby\Doctrine\EntityManager $em, $componentParams, $logger)
	{
		$this->em = $em;
		$this->componentParams = $componentParams;
		$this->logger = $logger;
	}

	public function render()
	{
		$formName = \Nette\Utils\Arrays::get($this->componentParams['params'], 'name', false);

		$this->getTemplate()->setFile(__DIR__. '/templates/Form.latte');
		$this->getTemplate()->formName = $formName;
		$this->getTemplate()->render();
	}

	public function createComponent($formName)
	{
		$formName = \Nette\Utils\Arrays::get($this->componentParams['params'], 'name', false);

		$componentName = '\App\PublicModule\Component\\' . $formName . 'FormFactory';
		$component = $this->getPresenter()->context->getByType($componentName);
//		$component = new $componentName($this->em, $this->logger);

		return $component->create($this->componentParams);
	}
}
