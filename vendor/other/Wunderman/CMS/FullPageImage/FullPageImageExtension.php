<?php

namespace Wunderman\CMS;

use Nette\DI\CompilerExtension;
use Nette\Utils\Arrays;

class FullPageImageExtension extends CompilerExtension
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

		$builder->getDefinition('privateComposePresenter')->addSetup(
			'addExtensionService',
			['fullPageImage', $this->prefix('@privateModuleService')]
		);

		$builder->getDefinition('publicComposePresenter')->addSetup(
			'setComposeComponentFactory',
			[
				'fullPageImage',
				$this->prefix('@fullPageImageFactory')
			]
		);
	}

}
