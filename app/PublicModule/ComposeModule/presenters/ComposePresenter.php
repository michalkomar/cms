<?php

namespace App\PublicModule\ComposeModule\Presenter;

use App;
use App\PublicModule\ComposeModule\Exception\ComposePresenterExcetpion;

final class ComposePresenter extends App\PublicModule\Presenters\BasePresenter
{

	/**
	 * @inject
	 * @var App\PublicModule\ComposeModule\Model\Service\Compose
	 */
	public $model;

	/**
	 * @inject
	 * @var \Tracy\ILogger
	 */
	public $logger;

	/**
	 * @var array
	 */
	private $composeComponentFactories = [];

	/**
	 * @var NULL|array
	 */
	private $pageParts = NULL;


	public function renderDefault($id)
	{
		$this->getTemplate()->pageParts = $this->getPageParts($id);
	}


	public function createComponent($name)
	{
		try {
			return $this->getComposeComponentFactory($name)->create();
		} catch (ComposePresenterExcetpion $e) {
			return parent::createComponent($name);
		}
	}


	public function setComposeComponentFactory($name, $factory)
	{
		$this->composeComponentFactories[$name] = $factory;
	}


	private function getComposeComponentFactory($name)
	{
		if (is_numeric($name)) {
			$id = (int) $name;
			$type = $this->pageParts[$id]['type'];

			if (!isset($this->composeComponentFactories[$type])) {
				throw new \InvalidArgumentException(
					"Component with type [$type] does not exist."
				);
			}

			return $this->composeComponentFactories[$type];
		}

		throw new ComposePresenterExcetpion;
	}


	/**
	 * Get cached page parts
	 * @param  int $id
	 * @return array
	 */
	private function getPageParts($id)
	{
		if (NULL === $this->pageParts) {
			$this->pageParts = $this->model->readArticleParts($id);
		}

		return $this->pageParts;
	}

}
