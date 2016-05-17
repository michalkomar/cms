<?php

namespace App\PrivateModule\UsersModule\Presenter;

/**
 * UsersPresenter
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class UsersPresenter extends \App\Presenters\SecuredPresenter
{

	use \Besir\CRUDTrait;

	/** @var \App\PrivateModule\UsersModule\Model\Service\Users $model */
	private $model;

	/**
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 */
	public function renderDefault()
	{
		$this->getTemplate()->passwordRegexp = '^.*(?=.{8,})(?=.*[a-zA-Z0-9])(?=.*[!"#$%^&*:\/;()><\?\-_=+,.]).*$';
		$this->getTemplate()->regions = $this->model->readRegions();
	}

	public function actionGeneratePasswords()
	{
		$this->getTemplate()->newUsers = $this->model->generatePasswordsForImportedUsers();
	}

	/**
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 */
	public function handleReadUser()
	{
		$this->methodSuffix = 'user';

		if ($this->user->isInRole('admin'))
		{
			$this->handleRead();
		}
		else
		{
			$this->handleRead(array('id' => $this->getUser()->getIdentity()->getId()));
		}
	}

	/**
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 */
	public function handleCreateUser()
	{
		$this->methodSuffix = 'user';
		$this->handleCreate();
	}

	public function handleUpdateUser()
	{
		$this->methodSuffix = 'user';
		$this->handleUpdate();
	}

	public function handleDestroyUser()
	{
		$this->methodSuffix = 'user';
		$this->handleDestroy();
	}

	public function handleReadRoles()
	{
		$this->methodSuffix = 'roles';
		$this->handleRead();
	}

	public function handleReadChannels()
	{
		$this->methodSuffix = 'channels';
		$this->handleRead();
	}

	public function handleResetPassword()
	{
		$this->methodSuffix = 'userPassword';
		$this->handleUpdate($this->getHttpRequest()->getPost('id'));
	}

	public function handleReadVillage()
	{
		$this->methodSuffix = 'village';
		$this->handleRead();
	}

	/**
	 * Return true if user role is in parents of inserted role or if is equal.
	 *
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 * @param array $userRole - used first index
	 * @return bool
	 */
	private function canEditThisUser($userRole)
	{
		$roleParents = $this->getUser()->getAuthorizator()->getRoleParents($this->getUser()->getRoles()[0]);
		return (in_array($userRole, $roleParents) or ($this->getUser()->getRoles()[0] === $userRole)) ? true : false;
	}

	// <editor-fold defaultstate="collapsed" desc="Getters">
	/**
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @return \App\PrivateModule\UsersModule\Model\Service\Users
	 */
	public function getModel()
	{
		return $this->model;
	}
	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="Dependency injection">
	/**
	 * Users service injection
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @param \App\PrivateModule\UsersModule\Model\Service\Users $model
	 */
	public function injectModelService(\App\PrivateModule\UsersModule\Model\Service\Users $model)
	{
		if ($this->model !== NULL)
		{
			throw new \Nette\InvalidStateException('Model Service has already been set.');
		}
		$this->model = $model;
	}
	// </editor-fold>
}
