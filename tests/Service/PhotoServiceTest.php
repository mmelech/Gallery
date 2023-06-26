<?php
/**
 * Photo service tests.
 */

namespace App\Tests\Service;

use App\Entity\Photo;
use App\Service\PhotoService;
use App\Service\GalleryService;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Gallery;
use App\Repository\GalleryRepository;




/**
 * Class PhotoServiceTest.
 */
class PhotoServiceTest extends KernelTestCase
{
    /**
     * Photo repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Photo service.
     */
    private ?PhotoService $photoService;


    /**
     * Gallery service.
     */
    private ?GalleryService $galleryService;

    /**
     * Set up test.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->photoService = $container->get(PhotoService::class);
        $container = static::getContainer();
        $this->entityManagerGallery = $container->get('doctrine.orm.entity_manager');
        $this->galleryService = $container->get(GalleryService::class);

    }

    /**
     * Test save.
     *
     * @throws ORMException
     */
    public function testSave(): void
    {
        // given
        $expectedPhoto = new Photo();
        $expectedPhoto->setTitle('Test Photo');
//        $expectedPhoto->setAuthor('Test Author');
        $expectedPhoto->setDate(new DateTimeImmutable());
        $expectedPhoto->setContent('Content');
        $gallery = $this->createGallery();
        $expectedPhoto->setGallery($gallery);

        // when
        $this->photoService->save($expectedPhoto);

        // then
        $expectedPhotoId = $expectedPhoto->getId();
        $resultPhoto = $this->entityManager->createQueryBuilder()
            ->select('photo')
            ->from(Photo::class, 'photo')
            ->where('photo.id = :id')
            ->setParameter(':id', $expectedPhotoId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedPhoto, $resultPhoto);
        $this->photoService->delete($expectedPhoto);
        $this->galleryService->delete($gallery);
    }

    /**
     * Test delete.
     *
     * @throws OptimisticLockException|ORMException
     */
    public function testDelete(): void
    {
        // given
        $photoToDelete = new Photo();
        $photoToDelete->setTitle('Test Photo');
//        $photoToDelete->setAuthor('Test Author');
        $photoToDelete->setDate(new DateTimeImmutable());
        $photoToDelete->setContent('Content');
        $gallery = $this->createGallery();
        $photoToDelete->setGallery($gallery);
        $this->entityManager->persist($photoToDelete);
        $this->entityManager->flush();
        $deletedPhotoId = $photoToDelete->getId();

        // when
        $this->photoService->delete($photoToDelete);

        // then
        $resultPhoto = $this->entityManager->createQueryBuilder()
            ->select('photo')
            ->from(Photo::class, 'photo')
            ->where('photo.id = :id')
            ->setParameter(':id', $deletedPhotoId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultPhoto);
        $this->galleryService->delete($gallery);

    }


    /**
     * Test pagination empty list.
     */
    public function testGetPaginatedList(): void
    {
        // given
        $page = 1;
        $dataSetSize = 3;
        $expectedResultSize = 10;

        $counter = 0;
        $gallery = $this->createGallery();
        while ($counter < $dataSetSize) {
            $photo = new Photo();
            $photo->setTitle('Test Photo #'.$counter);
//            $photo->setAuthor('Test Author #'.$counter);
            $photo->setGallery($gallery);
            $photo->setDate(new DateTimeImmutable());
            $photo->setContent('Content');
            $this->photoService->save($photo);

            ++$counter;
        }
        // when
        $result = $this->photoService->getPaginatedList($page);

        // then
        $this->assertEquals($expectedResultSize, count($result));
        $this->photoService->delete($photo);
        $this->galleryService->delete($gallery);

    }

    public function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->setTitle('Test Gallery 1');
        $this->galleryService->save($gallery);
        return $gallery;
    }


}