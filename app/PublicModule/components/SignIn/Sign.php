<?php

namespace App\PublicModule\Component;
use App\Entity\User;
use Kdyby\Events\EventArgsList;
use Kdyby\Events\EventManager;
use Nette\Application\UI\Form;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class Sign extends \Nette\Application\UI\Control
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/** @var \Kdyby\Events\EventManager */
	private $evm;

	/**
	 * @type \Kdyby\Facebook\Facebook
	 */
	private $facebook;


	public function __construct($parent, $name, \Kdyby\Doctrine\EntityManager $em, \Kdyby\Facebook\Facebook $facebook, EventManager $evm)
	{
		parent::__construct($parent, $name);
		$this->em = $em;
		$this->facebook = $facebook;
		$this->evm = $evm;
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @see Nette\Application\Control#render()
	 */
	public function render()
	{
		$this->getTemplate()->setFile(__DIR__.'/templates/Sign.latte');
		$this->getTemplate()->render();
	}

	public function createComponentSignInForm()
	{
		$form = new Form();

		$form->addText('username')
			->setRequired('Vyplň přihlašovací jméno.');

		$form->addPassword('password')
			->setRequired('Vyplň přihlašovací heslo.');

		$form->addSubmit('signin', 'Přihlaš mě');

		$form->onSuccess[] = array($this, 'signInProcess');

		return $form;
	}

	public function signInProcess(Form $form)
	{
		$values = (object) $form->getValues();

		$this->getPresenter()->user->login($values->username, $values->password);

		if ($this->getPresenter()->isAjax())
		{
			$this->getPresenter()->redrawControl();
		}
		else
		{
			$this->getPresenter()->redirect('this');
		}
	}

	public function handleOut()
	{
		$this->getPresenter()->getUser()->logout();

		if ($this->getPresenter()->isAjax())
		{
			$this->getPresenter()->redrawControl();
		}
		else
		{
			$this->getPresenter()->redirect('this');
		}
	}

	/** @return \Kdyby\Facebook\Dialog\LoginDialog */
	protected function createComponentFbLogin()
	{
		$dialog = $this->facebook->createDialog('login');
		/** @var \Kdyby\Facebook\Dialog\LoginDialog $dialog */

		$dialog->onResponse[] = function (\Kdyby\Facebook\Dialog\LoginDialog $dialog) {
			$fb = $dialog->getFacebook();
			if (!$fb->getUser()) {
				$this->getPresenter()->flashMessage("Sorry bro, facebook login je nějaký rozbitý. :-(");
				return;
			}

			/**
			 * If we get here, it means that the user was recognized
			 * and we can call the Facebook API
			 */

			try {
				$me = $fb->api('/me?fields=id,name,email,first_name,last_name');

				if (!$user = $this->userRepository()->findOneBy(array('facebookId' => $fb->getUser()))) {

					/**
					 * Variable $me contains all the public information about the user
					 * including facebook id, name and email, if he allowed you to see it.
					 */
					if ($user = $this->userRepository()->findOneBy(array('email' => $me->email)))
					{
						$user->setFacebookId($me->id);
						$this->em->flush();
					}
					else
					{
						$user = new User();
						$user->setUsername($me->email)
								->setEmail($me->email)
								->setFacebookId($me->id)
								->setName($me->first_name)
								->setSurname($me->last_name)
								->setFacebookAccessToken($fb->getAccessToken())
								->setFacebookOriginalUsername(1)
								->setRole($user::DEFAULT_ROLE)
								->setPassword('changeMe')
								->setPasswordNeedReset(1);

						$this->em->persist($user)->flush();
						$this->evm->dispatchEvent('\App\Entity\User::onCreate', new EventArgsList(array($user)));

						$fbImageUrl = 'https://graph.facebook.com/'.$me->id.'/picture?access_token='.$fb->getAccessToken();
						file_put_contents(__DIR__.'/../../../../www/images/user/'.$user->getId().'.jpg', file_get_contents($fbImageUrl));
					}

				}

				/**
				 * You should save the access token to database for later usage.
				 *
				 * You will need it when you'll want to call Facebook API,
				 * when the user is not logged in to your website,
				 * with the access token in his session.
				 */
//				$this->usersModel->updateFacebookAccessToken($fb->getUser(), $fb->getAccessToken());

				/**
				 * Nette\Security\User accepts not only textual credentials,
				 * but even an identity instance!
				 */
				$this->getPresenter()->getUser()->login(new \Nette\Security\Identity($user->id, $user->role, array(
					'name' => $user->name,
					'surname' => $user->surname,
					'requestedRole' => $user->requestedRole,
					'email' => $user->email,
					'status' => $user->status
				)));

				/**
				 * You can celebrate now! The user is authenticated :)
				 */

			} catch (\Kdyby\Facebook\FacebookApiException $e) {
				/**
				 * You might wanna know what happened, so let's log the exception.
				 *
				 * Rendering entire bluescreen is kind of slow task,
				 * so might wanna log only $e->getMessage(), it's up to you
				 */
				\Tracy\Debugger::log($e, 'facebook');
				$this->getPresenter()->flashMessage("Sorry bro, facebook to má nějaký hodně rozbitý. :-(");
			}

			if ($this->getPresenter()->isAjax())
			{
				$this->getPresenter()->redrawControl();
			}
			else
			{
//				$this->redirect('this');
			}
		};

		return $dialog;
	}

	public function userRepository()
	{
		return $this->em->getRepository('\App\Entity\User');
	}
}
