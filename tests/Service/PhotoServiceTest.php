<?php
/**
 * Photo service tests.
 */

namespace App\Tests\Service;

use App\Entity\Photo;
use App\Service\PhotoService;
use App\Service\GalleryService;
use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Entity\UserData;
use App\Repository\UserRepository;
use App\Service\UserService;
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

        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $this->removeUser();
        $user = new User();
        $user->setEmail('test103@example.com');
        $user->setRoles([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $userData = new UserData();
        $userData->setFirstname('user1');
        $userData->setLogin('user1');
        $user->setUserData($userData);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                'p@55w0rd'
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $expectedPhoto = new Photo();
        $expectedPhoto->setTitle('Test Photo');
        $expectedPhoto->setDate(new DateTimeImmutable());
        $expectedPhoto->setAuthor($user);
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
        $this->entityManager->remove($user);
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
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $this->removeUser();
        $user = new User();
        $user->setEmail('test109@example.com');
        $user->setRoles([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $userData = new UserData();
        $userData->setFirstname('user1');
        $userData->setLogin('user1');
        $user->setUserData($userData);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                'p@55w0rd'
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $photoToDelete = new Photo();
        $photoToDelete->setTitle('Test Photo');
        $photoToDelete->setAuthor($user);
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
        $this->entityManager->remove($user);
        $this->entityManager->flush();

    }



    public function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->setTitle('Test Gallery 1');
        $this->galleryService->save($gallery);
        return $gallery;
    }

    private function removeUser(): void
    {

        $userRepository = static::getContainer()->get(UserRepository::class);
        $entity = $userRepository->findOneBy(array('email' => 'test2@example.com'));


        if ($entity != null){
            $userRepository->delete($entity);
        }

    }



}