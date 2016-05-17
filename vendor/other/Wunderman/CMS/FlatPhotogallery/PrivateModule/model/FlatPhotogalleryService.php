<?php

	namespace App\PrivateModule\FlatPhotoGalleryModule\Model\Service;

	use App\Entity\FlatPhotoGalleryItem;
	use App\PrivateModule\AttachmentModule\Model\Service\Attachment;
	use Doctrine\ORM\Query;
	use Kdyby\Doctrine\EntityManager;
	use Nette\Utils\Arrays;

	/**
	 * Users service
	 * @author Petr Horacek <petr.horacek@wunderman.cz>
	 */
	class FlatPhotoGallery extends \Nette\Object
	{

		/**
		 * @var \Kdyby\Doctrine\EntityManager $em
		 */
		public $em;

		/**
		 * @var Attachment
		 */
		private $attachmentService;

		/**
		 * Construct
		 * @author Petr Horacek <petr.horacek@wunderman.cz>
		 *
		 * @param \Kdyby\Doctrine\EntityManager $entityManager
		 */
		public function __construct(EntityManager $entityManager, Attachment $attachmentService)
		{
			$this->attachmentService = $attachmentService;
			$this->em = $entityManager;
		}

		/**
		 * @return array
		 */
		public function readGalleries()
		{
			return $this->galleryRepository()->findBy(array('status' => 'ok'));
		}

		/**
		 * @param $id
		 *
		 * @return mixed|null|object
		 */
		public function readGallery($id)
		{
			return $this->galleryRepository()->findOneBy(array('status' => 'ok', 'id' => $id));
		}

		/**
		 * @param $galleryId
		 * @param bool $asArray
		 *
		 * @return array
		 */
		public function readGalleryItems($galleryId, $asArray = TRUE)
		{
			$items = $this->createReadGalleryItemsQuery($galleryId)->getQuery();

			if ($asArray) {
				$result = $items->getArrayResult();
				return $result ? $result : [];
			}

			return $items->getResult();
		}

		/**
		 * @param $galleryId
		 *
		 * @return \Doctrine\ORM\QueryBuilder
		 */
		private function createReadGalleryItemsQuery($galleryId)
		{
			return $this->galleryItemRepository()->createQueryBuilder('i')->select('i, attachment')->leftJoin('i.attachment',
					'attachment')->where('i.flatPhotoGallery = :galleryId')->setParameter('galleryId',
					$galleryId)->andWhere('i.status = :statusOk')->setParameter('statusOk',
					'ok')->orderBy('i.position');
		}

		/**
		 * @return mixed
		 */
		public function createNewGallery()
		{
			$gallery = (new \App\Entity\FlatPhotoGallery())->setName('newGallery');
			$this->em->persist($gallery)->flush();
			return $gallery;
		}

		/**
		 * @param $galleryId
		 */
		public function destroyGallery($galleryId)
		{
			$this->galleryRepository()->find($galleryId)->delete();
			$this->em->flush();
		}

		/**
		 * @param $image
		 * @param \App\Entity\FlatPhotoGallery $gallery
		 *
		 * @return array
		 * @throws \Doctrine\ORM\ORMException
		 */
		public function saveImageToGallery($image, \App\Entity\FlatPhotoGallery $gallery)
		{
			$imageId = $this->attachmentService->processFile($image);
			$lastImg = $this->galleryItemRepository()->findOneBy(['flatPhotoGallery' => $gallery->id],
				['position' => 'DESC']);
			$image = new FlatPhotoGalleryItem($gallery, $this->em->getReference('\App\Entity\Attachment', $imageId),
				$lastImg ? $lastImg->position + 1 : 1);

			$this->em->persist($image)->flush();

			return [
				'id' => $image->id,
				'attachment' => ['md5' => $image->attachment->md5],
				'position' => $image->position,
				'text' => NULL,
				'title' => NULL,
			];
		}

		/**
		 * @param $imageId
		 * @param $data
		 *
		 * @return array
		 */
		public function updateImage($imageId, $data)
		{
			$image = $this->galleryItemRepository()->findOneBy(['id' => $imageId]);

			$image->setTitle(Arrays::get($data, 'title', NULL))->setText(Arrays::get($data, 'text',
					NULL))->setPosition(Arrays::get($data, 'position', NULL));
			$this->em->flush();

			return [
				'id' => $image->id,
				'attachment' => ['md5' => $image->attachment->md5],
				'position' => $image->position,
				'text' => $image->text,
				'alt' => $image->title
			];
		}

		/**
		 * @param $imageId
		 *
		 * @return array
		 */
		public function destroyImage($imageId)
		{
			$image = $this->galleryItemRepository()->find($imageId);
			$image->delete();
			$this->em->flush();

			$this->updateImagesPositions($image->flatPhotoGallery->id);

			return $this->readGalleryItems($image->flatPhotoGallery->id);
		}

		/**
		 * @param $galleryId
		 */
		private function updateImagesPositions($galleryId)
		{
			$images = $this->createReadGalleryItemsQuery($galleryId)->orderBy('i.position',
				'ASC')->getQuery()->getResult();

			\Tracy\Debugger::log(count($images));
			$position = 1;
			foreach ($images as $image) {
				$image->setPosition($position);
				$position++;
			}

			$this->em->flush();
		}


		// <editor-fold defaultstate="collapsed" desc="Repositories">

		/**
		 * @return \Kdyby\Doctrine\EntityRepository
		 */
		private function galleryRepository()
		{
			return $this->em->getRepository('\App\Entity\FlatPhotoGallery');
		}

		/**
		 * @return \Kdyby\Doctrine\EntityRepository
		 */
		private function galleryItemRepository()
		{
			return $this->em->getRepository('\App\Entity\FlatPhotoGalleryItem');
		}

		// </editor-fold>

	}
