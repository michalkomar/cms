<?php
	/**
	 * Created by PhpStorm.
	 * User: horacekp
	 * Date: 24/02/16
	 * Time: 23:02
	 */

	namespace Wunderman\CMS\Feeds\Parsers;


	use Nette\Utils\Arrays;

	class Wordpress implements \Wunderman\CMS\Feeds\Parsers\IParser
	{
		/**
		 * @var string
		 */
		private $url;

		/**
		 * @var \GuzzleHttp\Client
		 */
		private $client;

		/**
		 * @var string
		 */
		private $feedXMLOutput;

		/**
		 * @var array
		 */
		private $feedArrayOutput;

		/**
		 * Wordpress constructor.
		 *
		 * @param \GuzzleHttp\Client $client
		 */
		public function __construct(\GuzzleHttp\Client $client)
		{
			$this->client = $client;
			$this->client->setDefaultOption('verify', FALSE);
		}

		/**
		 * @return array
		 */
		public function parse()
		{
			return $this->getFormattedOutputArray();
		}

		/**
		 * @return array
		 */
		private function getFormattedOutputArray()
		{
			$this->getFeedAsArray();

			$chanel = Arrays::get($this->feedArrayOutput, 'channel', FALSE);
			$items = Arrays::get($chanel, 'item', []);

			$feedData = [];
			foreach ($items as $item) {
				$feedData[] = [
					'datetime' => Arrays::get($item, 'pubDate', FALSE) ? new \DateTime($item['pubDate']) : NULL,
					'header' => Arrays::get($item, 'title', NULL),
					'perex' => Arrays::get($item, 'description', NULL),
					'detail' => Arrays::get($item, 'link', NULL),
					'id' => Arrays::get($item, 'guid', NULL),
				];
			}

			return $feedData;
		}

		private function getFeedAsArray()
		{
			$this->getFeedXML();
			$this->feedArrayOutput = json_decode(json_encode(simplexml_load_string($this->feedXMLOutput, 'SimpleXMLElement', LIBXML_NOCDATA)), TRUE);
		}

		private function getFeedXML()
		{
			$response = $this->client->get($this->url);
			$this->feedXMLOutput = (string)$response->getBody();
		}

		/**
		 * @inheritdoc
		 */
		public function setUp(array $options)
		{
			foreach ($options as $key => $value) {
				// key = 0 is default option from configuration (only one configuration value for parser, exactly URL)
				if (0 === $key) {
					$this->url = $value;
				} else {
					if (property_exists($this, $key)) {
						$this->$key = $value;
					}
				}
			}

			return $this;
		}
	}