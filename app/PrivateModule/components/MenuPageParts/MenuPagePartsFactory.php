<?php
	/**
	 * Created by PhpStorm.
	 * User: horacekp
	 * Date: 11/02/16
	 * Time: 15:24
	 */

	namespace App\PrivateModule\Components\MenuPageParts;


	use Nette\Application\UI\Control;
	use Nette\Application\UI\Presenter;

	class MenuPagePartsFactory
	{
		public $items;

		public function __construct($items)
		{
			$this->items = $items;
		}

		/**
		 * @return MenuPagePartsControl
		 */
		public function create()
		{
			return new MenuPagePartsControl($this->items);
		}
	}
