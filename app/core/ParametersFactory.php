<?php
/**
 * Created by PhpStorm.
 * User: horacekp
 * Date: 01/07/15
 * Time: 17:01
 */

namespace App;


class ParametersFactory extends \Nette\Object
{

	private $container;

	public function __construct(\Nette\DI\Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @param $parameter
	 *
	 * @return mixed
	 * Return null if parametr not found
	 */
	public function get($parameter)
	{
		return \Nette\Utils\Arrays::get($this->container->parameters, $parameter, null);
	}

}