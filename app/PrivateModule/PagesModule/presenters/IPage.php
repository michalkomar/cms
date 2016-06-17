<?php
/**
 * Created by PhpStorm.
 * User: horacekp
 * Date: 29/09/15
 * Time: 22:33
 */

namespace App\PrivateModule\PagesModule\Presenter;

use Nette\Forms\Controls\SubmitButton;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

interface IPage
{
	public function actionEdit($id);

	public function renderEdit($id);

	public function savePage(SubmitButton $button);

	public function editPage(Form $form, ArrayHash $values);

	public function createPage(Form $form, ArrayHash $values);

	/**
	 * @return array
	 */
	public function getPages();
}
