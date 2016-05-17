<?php

namespace App\PublicModule\FeedsCronModule\Presenter;

use App\PublicModule\FeedsModule\Model\Service\Feeds;
use Nette,
	App\Model;


/**
 * Homepage presenter.
 */
class FeedsCronPresenter extends \App\PublicModule\Presenters\BasePresenter
{

	/**
	 * @inject
	 * @var Feeds
	 */
	public $model;

	public $feeds;

	public function actionDefault()
	{
		$this->model->parseFeeds = $this->feeds;
		$this->model->parseFeeds();
	}

	public function renderDefault()
	{

	}

	public function setAvailableFeeds($feeds)
	{
		$this->feeds = $feeds;
	}
}
