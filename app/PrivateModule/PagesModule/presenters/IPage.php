<?php
/**
 * Created by PhpStorm.
 * User: horacekp
 * Date: 29/09/15
 * Time: 22:33
 */

namespace App\PrivateModule\PagesModule\Presenter;

use Nette\Forms\Controls\SubmitButton;
use Nette;
use Nette\Application\UI\Form;

interface IPage
{
	public function actionEdit($id);
	public function renderEdit($id);

	public function savePage(SubmitButton $button);

	public function editPage(Form $form, Nette\Utils\ArrayHash $values);

	public function createPage(Form $form, Nette\Utils\ArrayHash $values);

	/**
	 * @return array
	 */
	public function getPages();
}