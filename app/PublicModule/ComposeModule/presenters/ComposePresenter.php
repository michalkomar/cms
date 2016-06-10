<?php

namespace App\PublicModule\ComposeModule\Presenter;

use App;
use App\PublicModule\ComposeModule\Exception\ComposePresenterException;

final class ComposePresenter extends App\PublicModule\Presenters\BasePresenter
{

	/**
	 * @inject
	 * @var App\PublicModule\ComposeModule\Service\ComposeService
	 */
	public $composeService;

	/**
	 * @var array
	 */
	private $composeComponentFactories = [];

	/**
	 * @var NULL|array
	 */
	private $composeArticleItems = NULL;


	public function renderDefault($id)
	{
		$this->getTemplate()->composeArticleItems = $this->getComposeArticleItems($id);
	}


	public function createComponent($name)
	{
		try {
			return $this->getComposeComponentFactory($name)->create();
		} catch (ComposePresenterException $e) {
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
			$type = $this->composeArticleItems[$id]['type'];

			if (!isset($this->composeComponentFactories[$type])) {
				throw new \InvalidArgumentException(
					"Component with type [$type] does not exist."
				);
			}

			return $this->composeComponentFactories[$type];
		}

		throw new ComposePresenterException;
	}


	/**
	 * Get cached page parts
	 * @param  int $id
	 * @return array
	 */
	private function getComposeArticleItems($id)
	{
		if (NULL === $this->composeArticleItems) {
			$this->composeArticleItems = $this->composeService->readArticleParts($id);
		}

		return $this->composeArticleItems;
	}

}
