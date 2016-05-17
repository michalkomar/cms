<?php

namespace App\PublicModule\ComposeModule\Presenter;

use App\PublicModule\ComposeModule\Component\ComponentWrapperFactory;
use Nette;


/**
 * Homepage presenter.
 */
class ComposePresenter extends \App\PublicModule\Presenters\BasePresenter
{
	/**
	 * @inject
	 * @var \Kdyby\Doctrine\EntityManager
	 */
	public $em;

	/**
	 * @inject
	 * @var \App\PublicModule\ComposeModule\Model\Service\Compose
	 */
	public $model;

	/**
	 * @inject
	 * @var ComponentWrapperFactory
	 */
	public $componentWrapperFactory;

	/**
	 * @inject
	 * @var \Tracy\ILogger
	 */
	public $logger;

	public function renderDefault($id)
	{
		$this->getTemplate()->pageParts = $this->model->readArticleParts($id);
	}

	public function createComponentComponentWrapper()
	{
		return new  Nette\Application\UI\Multiplier(function($id) {
			$params = [];
			if (!is_null($this->getSignal()) && $this->getSignal()[1] === 'submit')
			{
				$params = $this->model->readPartParams($id);
			}

			return $this->componentWrapperFactory->create($id, $params);
		});
	}

}
