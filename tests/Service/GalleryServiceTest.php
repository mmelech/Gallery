<?php
/**
 * Gallery service tests.
 */

namespace App\Tests\Service;

use App\Entity\Gallery;
use App\Service\GalleryService;
use App\Service\GalleryServiceInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class GalleryServiceTest.
 */
class GalleryServiceTest extends KernelTestCase
{
    /**
     * Gallery repository.
     */
    private ?EntityManagerInterface $entityManager;

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
        $expectedGallery = new Gallery();
        $expectedGallery->setTitle('Test Gallery');
        // when
        $this->galleryService->save($expectedGallery);
        // then
        $expectedGalleryId = $expectedGallery->getId();
        $resultGallery = $this->entityManager->createQueryBuilder()
            ->select('gallery')
            ->from(Gallery::class, 'gallery')
            ->where('gallery.id = :id')
            ->setParameter(':id', $expectedGalleryId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedGallery, $resultGallery);
        $this->galleryService->delete($expectedGallery);
    }


    /**
     * Test delete.
     *
     * @throws OptimisticLockException|ORMException
     */
    public function testDelete(): void
    {
        // given
        $galleryToDelete = new Gallery();
        $galleryToDelete->setTitle('Test Gallery');
        $this->entityManager->persist($galleryToDelete);
        $this->entityManager->flush();
        $deletedGalleryId = $galleryToDelete->getId();

        // when
        $this->galleryService->delete($galleryToDelete);

        // then
        $resultGallery = $this->entityManager->createQueryBuilder()
            ->select('gallery')
            ->from(Gallery::class, 'gallery')
            ->where('gallery.id = :id')
            ->setParameter(':id', $deletedGalleryId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultGallery);
    }

    /**
     * Test find by id.
     *
     * @throws ORMException
     */
    public function testFindById(): void
    {
        // given
        $expectedGallery = new Gallery();
        $expectedGallery->setTitle('Test Gallery 1');
        $this->entityManager->persist($expectedGallery);
        $this->entityManager->flush();
        $expectedGalleryId = $expectedGallery->getId();

        // when
        $resultGallery = $this->galleryService->findOneById($expectedGalleryId);

        // then
        $this->assertEquals($expectedGallery, $resultGallery);
        $this->galleryService->delete($expectedGallery);
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
        while ($counter < $dataSetSize) {
            $gallery = new Gallery();
            $gallery->setTitle('Test Gallery #'.$counter);
            $this->entityManager->persist($gallery);

            ++$counter;
        }
        // when
        $result = $this->galleryService->getPaginatedList($page);

        // then
        $this->assertEquals($expectedResultSize, count($result));

    }
}


