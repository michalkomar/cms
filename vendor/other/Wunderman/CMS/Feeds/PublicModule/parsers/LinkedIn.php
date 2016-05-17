<?php
	namespace Wunderman\CMS\Feeds\Parsers;

	use \Nette\Utils\Arrays;

	class LinkedInParserException extends CmsParserException
	{}

	class LinkedIn implements \Wunderman\CMS\Feeds\Parsers\IParser
	{
		/**
		 * @var \GuzzleHttp\Client
		 */
		private $client;

		/**
		 * JSON response
		 * @var \stdClass
		 */
		private $feedArrayOutput;

		/**
		 * URL for updates company's profile
		 * @var string
		 */
		private $url;

		/**
		 * Access token for LinkedIn API
		 * @var string
		 */
		private $accessToken;

		/**
		 * Gets parsed feed data
		 * @return array
		 * @throws LinkedInParserException
		 */
		private function getFeedFormattedOutputArray()
		{
			$feedData = [];
			$this->getFeedContentAsArray();

			$values = Arrays::get($this->feedArrayOutput, 'values', []);

			foreach ($values as $item) {
				$content = Arrays::get($item, 'updateContent', []);
				$update = Arrays::get($content, 'companyStatusUpdate', []);
				$share = Arrays::get($update, 'share', []);

				$publishDate = Arrays::get($item, 'timestamp', FALSE)
					? new \DateTime(date('Y-m-d H:i:s', substr($item['timestamp'], 0, -3))) : NULL;
				$id = Arrays::get($share, 'id', NULL);
				$perex = Arrays::get($share, 'comment', NULL);

				$feedData[] = [
					'header' => NULL,
					'perex' => $perex,
					'datetime' => $publishDate,
					'detail' => 'https://www.linkedin.com/company/international-study-programs?trk=top_nav_home',
					'id' => $id
				];
			}
			unset($publishDate, $id, $detail, $content, $update, $share);

			return $feedData;
		}

		/**
		 * Gets API response as object
		 * @throws LinkedInParserException
		 */
		private function getFeedContentAsArray()
		{
			$response = $this->getFeedResponse();
			$this->feedArrayOutput = json_decode($response, TRUE);

			if (0 !== json_last_error()) {
				throw new LinkedInParserException("Invalid JSON response");
			}
		}

		/**
		 * Gets feed response
		 * @return string
		 */
		private function getFeedResponse()
		{
			// prepare HTTP query parameters for API call
			$parameters = [
				'oauth2_access_token' => $this->accessToken,
				'format' => 'json'
			];
			$url = $this->url . '?' . http_build_query($parameters);

			// get feed content
			$response = $this->client->get($url);

			return (string) $response->getBody();
		}

		/**
		 * LinkedIn parser constructor.
		 *
		 * @param \GuzzleHttp\Client $client
		 */
		public function __construct(\GuzzleHttp\Client $client)
		{
			$this->client = $client;
			// disable SSL verification
			$this->client->setDefaultOption('verify', FALSE);
		}

		/**
		 * @inheritdoc
		 */
		public function parse()
		{
			return $this->getFeedFormattedOutputArray();
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