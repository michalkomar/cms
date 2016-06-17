<?php
	/**
	 * Created by PhpStorm.
	 * User: horacekp
	 * Date: 27/01/16
	 * Time: 16:31
	 */

	namespace App\PrivateModule\PagesModule\Presenter;


	use Nette\Application\UI\Form;
	use Nette\Application\UI\Presenter;
	use Nette\Utils\ArrayHash;

	interface ComposedPageExtension
	{
		const ITEM_CONTAINER = 'item';

		/**
		 * Prepare adding new item, add imputs to global form etc.
		 *
		 * @param Form $button
		 *
		 * @return mixed
		 */
		public function addItem(Form &$form);

		/**
		 * @param Form $form
		 * @param array $editItemsParams
		 *
		 * @return mixed
		 */
		public function editItemParams(Form &$form, $editItemParams);

		/**
		 * Make magic for creating new item, e.g. save new image and return his params for save.
		 *
		 * @var array $values Form values
		 *
		 * @return array Associated array in pair [ propertyName => value ] for store to the database
		 */
		public function processNew(Form $form, ArrayHash $values);

		/**
		 * Editing current edited item,
		 *
		 * @var array $values Form values
		 * @var array $itemParams @TODO fill description
		 *
		 * @return array
		 */
		public function processEdit(Form $form, ArrayHash $values, $itemParams);

		/**
		 * Compute anchor for item on the page
		 *
		 * @var object
		 *
		 * @return string
		 */
		public function getAnchor($item);

		/**
		 * Return real path to addItemTemplate
		 *
		 * @return string
		 */
		public function getAddItemTemplate();

		/**
		 * Return real path to editItemTemplate
		 *
		 * @return string
		 */
		public function getEditItemTemplate();


	}