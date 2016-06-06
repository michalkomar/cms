<?php
	/**
	 * Created by PhpStorm.
	 * User: horacekp
	 * Date: 27/01/16
	 * Time: 10:40
	 */

	namespace Wunderman\CMS;


	use Nette\DI\CompilerExtension;
	use Nette\Utils\Arrays;

	class Form extends CompilerExtension
	{
		public function loadConfiguration()
		{
			$builder = $this->getContainerBuilder();
			$extensionConfig = $this->loadFromFile(__DIR__ . '/config.neon');
			$this->compiler->parseServices($builder, $extensionConfig, $this->name);

			$builder->parameters = Arrays::mergeTree($builder->parameters,
				Arrays::get($extensionConfig, 'parameters', []));
		}

		public function beforeCompile()
		{
			$builder = $this->getContainerBuilder();
			$builder->getDefinition('privateComposePresenter')->addSetup('addExtensionService',
				['form', $this->prefix('@privateModuleService')]);
		}
	}
