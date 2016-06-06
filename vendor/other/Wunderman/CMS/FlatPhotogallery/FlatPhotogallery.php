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

	class FlatPhotoGallery extends CompilerExtension
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

			$builder->getDefinition("webloader.cssPrivateFiles")->addSetup('addFile', [realpath(__DIR__.'/css/private.css')]);
			$builder->getDefinition("webloader.cssPrivateFiles")->addSetup('addFile', [realpath(__DIR__.'/css/public.css')]);
			$builder->getDefinition("webloader.cssPublicFiles")->addSetup('addFile', [realpath(__DIR__.'/css/public.css')]);

			$builder->getDefinition("webloader.jsPublicFiles")->addSetup('addFile', [realpath(__DIR__.'/js/swipeFunc.js')]);
			$builder->getDefinition("webloader.jsPublicFiles")->addSetup('addFile', [realpath(__DIR__.'/js/public.js')]);

			$builder->getDefinition('privateComposePresenter')->addSetup('addExtensionService',
				['flatPhotoGallery', $this->prefix('@privateModuleService')]);

			// extending ACL resources
			$builder->getDefinition('authorizator')->addSetup('addResource', ['Private:FlatPhotoGallery:List']);
			$builder->getDefinition('authorizator')->addSetup('addResource', ['Private:FlatPhotoGallery:New']);
			$builder->getDefinition('authorizator')->addSetup('addResource', ['Private:FlatPhotoGallery:Edit']);
		}
	}
