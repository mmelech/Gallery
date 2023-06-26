<?php
/**
 * Comment
service test
 */
namespace App\Tests\Service;

use App\Entity\Enum\UserRole;
use App\Entity\Photo;
use App\Entity\Gallery;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\UserData;
use App\Repository\UserRepository;
use App\Service\PhotoService;
use App\Service\GalleryService;
use App\Service\CommentService;
use App\Service\UserService;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class CommentServiceTest
 */
class CommentServiceTest extends KernelTestCase
{

    private ?EntityManagerInterface $entityManager;

    private ?CommentService $commentService;
    /**
     * Gallery service.
     */
    private ?GalleryService $galleryService;


    /**
     * Set up test
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->commentService = $container->get(CommentService::class);
        $this->galleryService = $container->get(GalleryService::class);

    }


    /**
     * Save test
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testSave(): void
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $this->removeUser();
        $user = new User();
        $user->setEmail('test2@example.com');
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
//        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $testGallery = $this->createGallery();
        $this->entityManager->persist($testGallery);
        $this->entityManager->flush();

        $testPhoto = new Photo();
        $testPhoto->setTitle('Test Photo ');
//        $testPhoto->setAuthor('Test Author ');
        $testPhoto->setGallery($testGallery);
        $testPhoto->setDate(new DateTimeImmutable());
        $testPhoto->setContent('Content');
        $this->entityManager->persist($testPhoto);
        $this->entityManager->flush();

        $expectedComment = new Comment
        ();
        $expectedComment->setPhoto($testPhoto);
        $expectedComment->setAuthor($user);
        $expectedComment->setContent('Comment
 Test');

        //when
        $this->commentService->save($expectedComment);

        //then
        $expectedCommentId = $expectedComment->getId();
        $resultComment = $this->entityManager->createQueryBuilder()
            ->select('comment
')
            ->from(Comment
            ::class, 'comment
')
            ->where('comment.id = :id')
            ->setParameter(':id', $expectedCommentId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->galleryService->delete($testGallery);
        $this->assertEquals($expectedComment->getContent(), $resultComment->getContent());
    }

//    /**
//     * Delete test
//     *
//     * @throws \Doctrine\ORM\NonUniqueResultException
//     */
//    public function testDelete(): void
//    {
//        //given
//        $testUser = new User();
//        $testUser->setEmail('testUser3@example.com');
//        $testUser->setPassword('testUser1234');
//        $testUser->setRoles(['ROLES_USER']);
//        $this->entityManager->persist($testUser);
//        $this->entityManager->flush();
//
//        $testCategory = new Category();
//        $testCategory->setCreatedAt(new \DateTimeImmutable());
//        $testCategory->setUpdatedAt(new \DateTimeImmutable());
//        $testCategory->setTitle('Category Test');
//        $this->entityManager->persist($testCategory);
//        $this->entityManager->flush();
//
//        $testBook = new Book();
//        $testBook->setCategory($testCategory);
//        $testBook->setTitle('Book Test');
//        $testBook->setAuthor('Author Test');
//        $this->entityManager->persist($testBook);
//        $this->entityManager->flush();
//
//        $commentToDelete = new Comment
//        ();
//        $commentToDelete->setContent('Comment
// Test');
//        $commentToDelete->setBook($testBook);
//        $commentToDelete->setAuthor($testUser);
//        $this->entityManager->persist($commentToDelete);
//        $this->entityManager->flush();
//        $deletedCommentId = $commentToDelete->getId();
//
//        //when
//        $this->commentService->delete($commentToDelete);
//
//        //then
//        $resultComment = $this->entityManager->createQueryBuilder()
//            ->select('comment
//')
//            ->from(Comment
//            ::class, 'comment
//')
//            ->where('comment
//.id = :id')
//            ->setParameter(':id', $deletedCommentId, Types::INTEGER)
//            ->getQuery()
//            ->getOneOrNullResult();
//
//        $this->assertNull($resultComment);
//
//    }
    private function removeUser(): void
    {

        $userRepository = static::getContainer()->get(UserRepository::class);
        $entity = $userRepository->findOneBy(array('email' => 'test2@example.com'));


        if ($entity != null){
            $userRepository->delete($entity);
        }

    }

    private function createUser(array $roles): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $user = new User();
        $user->setEmail('test2@example.com');
        $user->setRoles($roles);
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
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->save($user);

        return $user;
    }

    public function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->setTitle('Test Gallery 2');
        $this->galleryService->save($gallery);
        return $gallery;
    }

}