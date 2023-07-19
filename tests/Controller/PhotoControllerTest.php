<?php
/**
 * Photo Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Photo;
use App\Entity\UserData;
use App\Service\GalleryService;
use App\Entity\Gallery;
use App\Service\PhotoService;
use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;


/**
 * Class PhotoControllerTest.
 */
class PhotoControllerTest extends WebTestCase
{

    private ?EntityManagerInterface $entityManager;

    /**
     * Gallery service.
     */
    private ?GalleryService $galleryService;


    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/photo';

    /**
     * Test client.
     */
    private KernelBrowser $httpClient;

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');

        $this->galleryService = $container->get(GalleryService::class);
        $this->photoService = $container->get(PhotoService::class);

    }

    /**
     * Test index route for anonymous user.
     */
    public function testIndexRouteAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 200;

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route for admin user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testIndexRouteAdminUser(): void
    {
        // given
        $this->removeUser();
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test new route for anonymous user.
     */
    public function testNewRouteAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 302;

        // when
        $this->httpClient->request('GET', '/photo/create');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test edit route for anonymous user.
     */
    public function testEditRouteAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 301;
        $expectedPhoto = new Photo();
        $expectedPhoto->setTitle('Test Photo');
        $newUserData = new UserData();
        $newUser = new User();
        $newUser->setEmail('testEmail2@example.com');
        $newUser->setPassword('test1234');
        $newUser->setUserData($newUserData);
        $expectedPhoto->setAuthor($newUser);
        $this->entityManager->persist($newUser);
        $expectedPhoto->setDate(new DateTimeImmutable());
        $gallery = $this->createGallery();
        $expectedPhoto->setContent('Content');
        $expectedPhoto->setGallery($gallery);
        $this->entityManager->persist($expectedPhoto);
        $this->entityManager->flush();
        $expectedPhotoId = $expectedPhoto->getId();

        // when
        $this->httpClient->request('GET', '/photo/' . strval($expectedPhotoId). '/edit/');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
        $this->photoService->delete($expectedPhoto);

        $this->entityManager->remove($newUser);
        $this->entityManager->flush();
        $this->galleryService->delete($gallery);
    }

    /**
     * Test show route for admin user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testShowRouteAdminUser(): void
    {
        // given
        $this->removeUser();
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);
        $expectedStatusCode = 200;
        $expectedPhoto = new Photo();
        $expectedPhoto->setTitle('Test Photo');
        $newUserData = new UserData();
        $newUser = new User();
        $newUser->setEmail('testEmail1@example.com');
        $newUser->setPassword('test1234');
        $newUser->setUserData($newUserData);
        $expectedPhoto->setAuthor($newUser);
        $this->entityManager->persist($newUser);
        $expectedPhoto->setFilename('abc');
        $expectedPhoto->setDate(new DateTimeImmutable());
        $gallery = $this->createGallery();
        $expectedPhoto->setContent('Content');
        $expectedPhoto->setGallery($gallery);
        $this->entityManager->persist($expectedPhoto);
        $this->entityManager->flush();
        $expectedPhotoId = $expectedPhoto->getId();

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', '/photo/' . strval($expectedPhotoId));
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
        $this->photoService->delete($expectedPhoto);
        $this->entityManager->remove($newUser);
        $this->entityManager->flush();
        $this->galleryService->delete($gallery);
    }


    /**
     * Create user.
     *
     * @param array $roles User roles
     *
     * @return User User entity
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
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

    private function removeUser(): void
    {

        $userRepository = static::getContainer()->get(UserRepository::class);
        $entity = $userRepository->findOneBy(array('email' => 'test2@example.com'));


        if ($entity != null){
            $userRepository->delete($entity);
        }

    }


    public function createGallery(): Gallery
    {
        $gallery = new Gallery();
        $gallery->setTitle('Test Gallery1');
        $this->galleryService->save($gallery);
        return $gallery;
    }

}