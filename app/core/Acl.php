<?php

/**
 * @author Petr Horacek <sirbesir@gmail.com>
 */

namespace Security;

use \Nette\Security\Permission;

class Acl extends \Nette\Security\Permission
{

	/**
	 * Acl constructor.
	 */
	public function __construct()
	{
		$this->createRoles();
		$this->createResources();
		$this->createPrivileges();
	}


	/**
	 * Adding user roles for ACL
	 */
	private function createRoles()
	{
		$this->addRole('guest');

		$this->addRole('admin');
	}

	/**
	 * Adding basic resources for ACL
	 *
	 * Extending resources is available via extensions
	 */
	private function createResources()
	{
		$this->addResource('Private:Dashboard:Dashboard');

		$this->addResource('Private:Users:Sign');
		$this->addResource('Private:Users:Users');

		$this->addResource('Private:Pages:NewPage');
		$this->addResource('Private:Pages:TextPage');
		$this->addResource('Private:Pages:EditPage');
		$this->addResource('Private:Pages:Compose');
		$this->addResource('Private:Pages:UrlPage');
	}

	/**
	 * Creating basic privileges
	 */
	private function createPrivileges()
	{
		// guest
		$this->allow('guest', 'Private:Users:Sign', NULL);


		// admin
		$this->allow('admin', Permission::ALL, Permission::ALL);
	}
}
