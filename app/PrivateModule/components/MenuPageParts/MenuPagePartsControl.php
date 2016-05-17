<?php
	/**
	 * Created by PhpStorm.
	 * User: horacekp
	 * Date: 11/02/16
	 * Time: 15:24
	 */

	namespace App\PrivateModule\Components\MenuPageParts;


	class MenuPagePartsControl extends \Nette\Application\UI\Control
	{

		/**
		 * @car array
		 */
		private $items;

		public function __construct($items)
		{
			$this->items = $items;
		}

		public function render()
		{
			$this->getTemplate()->setFile(__DIR__.'/templates/default.latte');
			$this->getTemplate()->items = $this->items;
			$this->getTemplate()->render();
		}
	}
