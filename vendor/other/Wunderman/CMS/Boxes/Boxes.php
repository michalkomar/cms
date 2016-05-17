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

	class Boxes extends CompilerExtension
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
			$builder->getDefinition('composedPagePresenter')->addSetup('addExtensionService',
				['boxes', $this->prefix('@privateModuleService')]);

			// extending ACL resources
			$builder->getDefinition('authorizator')->addSetup('addResource', ['Private:Boxes:List']);
			$builder->getDefinition('authorizator')->addSetup('addResource', ['Private:Boxes:New']);
			$builder->getDefinition('authorizator')->addSetup('addResource', ['Private:Boxes:Edit']);

			$builder->getDefinition("webloader.cssPublicFiles")->addSetup('addFile', [realpath(__DIR__.'/css/public.css')]);
			$builder->getDefinition("webloader.cssPrivateFiles")->addSetup('addFile', [realpath(__DIR__.'/css/private.css')]);
		}
	}
