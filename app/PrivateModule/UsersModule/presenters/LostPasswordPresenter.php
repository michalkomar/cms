<?php

namespace App\PrivateModule\UsersModule\Presenter;
use Nette\Application\ForbiddenRequestException;
use Doctrine\DBAL\Exception\ConnectionException;

/**
 * @author Petr Besir Horacek <sirbesir@gmail.com>
 * Sign in/out presenters.
 */
class LostPasswordPresenter extends \App\Presenters\BasePresenter
{

	/**
	 * @inject
	 * @var \App\PrivateModule\UsersModule\Model\Service\Users
	 */
	public $model;

	/**
	 * Lost password form factory.
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentLostPasswordForm()
	{
		$form = new \Nette\Application\UI\Form;
		$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Vyplňte uživatelské jméno.');

		$form->addSubmit('send', 'Odeslat nové heslo');

		$form->onError[] = array($this, 'errorForm');
		$form->onSuccess[] = array($this, 'signInFormSubmitted');

		return $form;
	}


	/**
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 * @param \Nette\Application\UI\Form $form
	 * @return void
	 */
	public function signInFormSubmitted(\Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();

		$user = $this->model->findUser($values['username']);

		if (!$user)
		{
			$this->flashMessage('Uživatel s tímto jménem neexistuje');
			$this->redirect('this');
		}

		$this->model->updateUserPassword($user->getId());

		$this->flashMessage('Nové heslo bylo odesláno na Váš e-mail', 'success');
		$this->redirect(':Private:Users:Sign:in');
	}

	/**
	 * Add form errors to flashes
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 * @param \Nette\Application\UI\Form $form
	 */
	public function errorForm(\Nette\Application\UI\Form $form)
	{
		foreach ($form->getErrors() as $error)
		{
			$this->getPresenter()->flashMessage($error, 'error');
		}
	}

}
