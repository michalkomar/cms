<?php

namespace App\PublicModule\TextModule\Presenter;

use Nette,
	App\Model;


/**
 * Homepage presenter.
 */
class TextPresenter extends \App\PublicModule\Presenters\BasePresenter
{

	/**
	 * @inject
	 * @var \App\PublicModule\TextModule\Model\Service\Text
	 */
	public $model;

	public function renderDefault($id)
	{
		$this->getTemplate()->article = $this->model->readArticle($id);
	}

}
