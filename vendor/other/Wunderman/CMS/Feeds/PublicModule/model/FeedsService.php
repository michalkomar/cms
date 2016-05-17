<?php

	namespace App\PublicModule\FeedsModule\Model\Service;

	use App\Entity\FeedItem;
	use Doctrine\ORM\Query;
	use Kdyby\Doctrine\EntityManager;

	/**
	 * Users service
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 */
	class Feeds extends \Nette\Object
	{

		/**
		 * @var \Kdyby\Doctrine\EntityManager $em
		 */
		public $em;

		/**
		 * @var array
		 */
		public $parseFeeds;

		/**
		 * @var array
		 */
		private $parsers;

		/**
		 * @var array
		 */
		private $feedData;

		/**
		 * @var string
		 */
		private $feed;

		/**
		 * Construct
		 * @author Petr Horacek <petr.horacek@wunderman.cz>
		 *
		 * @param \Kdyby\Doctrine\EntityManager $entityManager
		 */
		public function __construct(EntityManager $entityManager)
		{
			$this->em = $entityManager;
		}

		public function parseFeeds()
		{
			foreach ($this->parsers as $key => $parser) {
				$this->feedData = $parser->parse();
				$this->feed = $key;
				$this->storeFeedData();
			}
		}

		private function storeFeedData()
		{
			$this->em->beginTransaction();

			foreach ($this->feedData as $feedRecord) {
				$feedItemIdHash = md5($feedRecord['id']);

				$record = $this->feedItemRepository()->findOneBy([
					'type' => $this->feed,
					'feedItemId' => $feedItemIdHash,
				]);

				if (! $record) {
					$record = new FeedItem($this->feed, $feedRecord['datetime'], $feedRecord['header'],
						$feedRecord['perex'], $feedRecord['detail'], $feedItemIdHash);
					$this->em->persist($record);
				}
			}

			$this->em->flush()->commit();
		}

		/**
		 * @param $parsers
		 */
		public function setAvailableFeedsParsers($parsers)
		{
			$this->parsers = $parsers;
		}

		// <editor-fold defaultstate="collapsed" desc="Repositories">
		/**
		 * @return \Kdyby\Doctrine\EntityRepository
		 */
		public function feedItemRepository()
		{
			return $this->em->getRepository('\App\Entity\FeedItem');
		}
		// </editor-fold>
	}
