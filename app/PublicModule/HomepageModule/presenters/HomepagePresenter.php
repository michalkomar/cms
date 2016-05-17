<?php

namespace App\PublicModule\HomepageModule\Presenter;

use App\PublicModule\Component\Boxes;
use App\PublicModule\Component\Carousel;
use App\PublicModule\Component\Feeds;
use Nette,
	App\Model;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends \App\PublicModule\Presenters\BasePresenter
{

	/**
	 * @inject
	 * @var \Kdyby\Doctrine\EntityManager
	 */
	public $em;

	public function renderDefault()
	{
	}

	public function createComponentBoxes()
	{
		return new Nette\Application\UI\Multiplier(function($id){
			return new Boxes($this->em);
		});
	}

	public function createComponentCarousel()
	{
		return new Nette\Application\UI\Multiplier(function($id){
			return new Carousel($this->em);
		});
	}

	public function createComponentFeeds()
	{
		return new Feeds($this->em);
	}
}
