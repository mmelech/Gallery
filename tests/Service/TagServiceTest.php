<?php
/**
 * Tag service tests.
 */

namespace App\Tests\Service;

use App\Entity\Tag;
use App\Service\TagService;
use App\Service\TagServiceInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TagServiceTest.
 */
class TagServiceTest extends KernelTestCase
{
    /**
     * Tag repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Cat service.
     */
    private ?TagServiceInterface $tagService;

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
        $this->tagService = $container->get(TagService::class);
    }

    /**
     * Test save.
     *
     * @throws ORMException
     */
    public function testSave(): void
    {
        // given
        $expectedTag = new Tag();
        $expectedTag->setTitle('Test Tag');

        // when
        $this->tagService->save($expectedTag);

        // then
        $expectedTagId = $expectedTag->getId();
        $resultTag = $this->entityManager->createQueryBuilder()
            ->select('tag')
            ->from(Tag::class, 'tag')
            ->where('tag.id = :id')
            ->setParameter(':id', $expectedTagId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedTag, $resultTag);
    }

    /**
     * Test delete.
     *
     * @throws OptimisticLockException|ORMException
     */
    public function testDelete(): void
    {
        // given
        $tagToDelete = new Tag();
        $tagToDelete->setTitle('Test Tag');
        $this->entityManager->persist($tagToDelete);
        $this->entityManager->flush();
        $deletedTagId = $tagToDelete->getId();

        // when
        $this->tagService->delete($tagToDelete);

        // then
        $resultTag = $this->entityManager->createQueryBuilder()
            ->select('tag')
            ->from(Tag::class, 'tag')
            ->where('tag.id = :id')
            ->setParameter(':id', $deletedTagId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultTag);
    }

    /**
     * Test find by id.
     *
     * @throws ORMException
     */
    public function testFindById(): void
    {
        // given
        $expectedTag = new Tag();
        $expectedTag->setTitle('Test Tag');
        $this->entityManager->persist($expectedTag);
        $this->entityManager->flush();
        $expectedTagId = $expectedTag->getId();

        // when
        $resultTag = $this->tagService->findOneById($expectedTagId);

        // then
        $this->assertEquals($expectedTag, $resultTag);
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
            $tag = new Tag();
            $tag->setTitle('Test Tag #'.$counter);
            $this->tagService->save($tag);

            ++$counter;
        }

        // when
        $result = $this->tagService->getPaginatedList($page);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }

    // other tests for paginated list
}
