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

	class YoutubeVideo extends CompilerExtension
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

			// Adding custom CSS for extension
			$builder->getDefinition("webloader.cssPublicFiles")->addSetup('addFile', [realpath(__DIR__.'/css/main.css')]);
			$builder->getDefinition("webloader.cssPrivateFiles")->addSetup('addFile', [realpath(__DIR__.'/css/main.css')]);

			$builder->getDefinition('privateComposePresenter')->addSetup('addExtensionService',
				['youtubeVideo', $this->prefix('@privateModuleService')]);
		}
	}
