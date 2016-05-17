<?php
	/**
	 * Created by PhpStorm.
	 * User: horacekp
	 * Date: 27/01/16
	 * Time: 10:40
	 */

	namespace Wunderman\CMS;

	use Nette\DI\CompilerExtension;
	use Nette\DI\ContainerBuilder;
	use Nette\Utils\Arrays;

	class Feeds extends CompilerExtension
	{
		private $defaults = [
			'feeds' => [
				'wordpress' => FALSE,
				'linkedin' => FALSE,
			],
			'parsers' => [
				'linkedin' => 'Wunderman\CMS\Feeds\Parsers\LinkedIn',
				'wordpress' => 'Wunderman\CMS\Feeds\Parsers\Wordpress',
			]
		];

		protected $config = [];

		public function loadConfiguration()
		{
			$this->config = $this->getConfig($this->defaults);

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
				['feeds', $this->prefix('@privateModuleService')]);

			$parsers = $this->createParsersServices($builder);

			$builder->getDefinition($this->prefix('feedsCronPresenter'))->addSetup('setAvailableFeeds', [$this->config['feeds']]);
			$builder->getDefinition($this->prefix('feedsService'))->addSetup('setAvailableFeedsParsers', [$parsers]);

			// extending ACL resources
			$builder->getDefinition('authorizator')->addSetup('addResource', ['Private:Feeds:List']);
		}

		/**
		 * @param ContainerBuilder $builder
		 *
		 * @return array
		 */
		private function createParsersServices(ContainerBuilder $builder)
		{
			$parsers = [];

			foreach ($this->config['parsers'] as $key => $parser) {
				if (isset($this->config['feeds'][$key]) && $this->config['feeds'][$key])
				{
					$options = $this->config['feeds'][$key];
					settype($options, 'array');

					$builder->addDefinition($this->prefix($key.'Parser'))
						->addSetup('setUp', [$options])
						->setClass($parser)
						->setAutowired(FALSE);
					$parsers[$key] = $this->prefix('@'.$key.'Parser');
				}
				else
				{
					// @TODO log info about not setup url for parser
				}
			}

			return $parsers;
		}
	}
