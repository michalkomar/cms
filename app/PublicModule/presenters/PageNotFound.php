<?php

	namespace App\PublicModule\Presenter;

	use Nette;
	use App\Model;
	use Tracy\ILogger;


	class PageNotFoundPresenter extends \App\PublicModule\Presenters\BasePresenter
	{
		/**
		 * @param  Exception
		 * @return void
		 */
		public function renderDefault()
		{
			$this->getHttpResponse()->setCode(404);
		}
	}
