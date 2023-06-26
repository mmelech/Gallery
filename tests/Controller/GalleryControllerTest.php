<?php
/**
 * Gallery Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\User;
use App\Entity\UserData;
use App\Repository\UserRepository;
use App\Service\GalleryService;
use DateTimeImmutable;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class GalleryControllerTest.
 */
class GalleryControllerTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager;

    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/category';


    /**
     * Gallery service.
     */
    private ?GalleryService $galleryService;


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
    }

    /**
     * Test index route for anonymous user.
     */
    public function testIndexRouteAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 200;
        // when
        $this->httpClient->request('GET', '/gallery');
        $resultHttpStatusCode = $this->httpClient->getResponse()->getStatusCode();
        // then
        $this->assertEquals($expectedStatusCode, $resultHttpStatusCode);
    }

    /**
     * Test new route for anonymous user.
     */
    public function testNewRouteAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 302;

        // when
        $this->httpClient->request('GET', '/gallery/create');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route for Normal user.
     *
     */
    public function testIndexRouteNormalUser(): void
    {
        // given
        $this->removeUser();
        $expectedStatusCode = 200;
        $user = $this->createUser([UserRole::ROLE_USER->value]);
        $this->httpClient->loginUser($user);

        // when
        $this->httpClient->request('GET', '/gallery');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultStatusCode);
    }

    /**
     * Test new route for Normal user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testNewRouteNormalUser(): void
    {
        // given
        $this->removeUser();
        $expectedStatusCode = 200;
        $user = $this->createUser([UserRole::ROLE_USER->value]);
        $this->httpClient->loginUser($user);

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', '/gallery/create');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route for admin user
     *
     */
    public function testIndexRouteAdminUser(): void
    {
        // given
        $this->removeUser();
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        // when
        $this->httpClient->request('GET', '/gallery');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultStatusCode);
    }

    /**
     * Test new route for admin user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testNewRouteAdminUser(): void
    {
        // given
        $this->removeUser();
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', '/gallery/create');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test edit route for admin user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testEditRouteAdminUser(): void
    {
        $this->removeUser();
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);
        $expectedGallery = new Gallery();
        $expectedGallery->setTitle('Test Gallery Edited');
        $this->galleryService->save($expectedGallery);
        $expectedGalleryId = $expectedGallery->getId();

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', "/gallery/" . strval($expectedGalleryId) .'/edit');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $this->galleryService->delete($expectedGallery);

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test edit route for Normal user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testEditRouteNormalUser(): void
    {
        $this->removeUser();
        $expectedStatusCode = 200;
        $user = $this->createUser([UserRole::ROLE_USER->value]);
        $this->httpClient->loginUser($user);
        $expectedGallery = new Gallery();
        $expectedGallery->setTitle('Test Gallery Edited');
        $this->galleryService->save($expectedGallery);
        $expectedGalleryId = $expectedGallery->getId();

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', "/gallery/" . strval($expectedGalleryId) .'/edit');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $this->galleryService->delete($expectedGallery);

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test edit route for anonymous user.
     */
    public function testEditRouteAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 302;
        $expectedGallery = new Gallery();
        $expectedGallery->setTitle('Test Gallery Edited');
        $this->galleryService->save($expectedGallery);
        $expectedGalleryId = $expectedGallery->getId();

        // when
        $this->httpClient->request('GET', '/gallery/' . strval($expectedGalleryId) .'/edit');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $this->galleryService->delete($expectedGallery);

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
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
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);
        $expectedGallery = new Gallery();
        $expectedGallery->setTitle('Test Gallery Edited');
        $this->galleryService->save($expectedGallery);
        $expectedGalleryId = $expectedGallery->getId();

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', '/gallery/' .strval($expectedGalleryId));
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $this->galleryService->delete($expectedGallery);

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test show route for Normal user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testShowRouteNormalUser(): void
    {
        // given
        $this->removeUser();
        $expectedStatusCode = 200;
        $user = $this->createUser([UserRole::ROLE_USER->value]);
        $this->httpClient->loginUser($user);
        $expectedGallery = new Gallery();
        $expectedGallery->setTitle('Test Gallery Edited');
        $this->galleryService->save($expectedGallery);
        $expectedGalleryId = $expectedGallery->getId();

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', '/gallery/' .strval($expectedGalleryId));
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $this->galleryService->delete($expectedGallery);

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test show route for anonymous user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testShowRouteAnonymousUser(): void
    {
        // given
        $expectedGallery = new Gallery();
        $expectedGallery->setTitle('Test Gallery Edited');
        $this->galleryService->save($expectedGallery);
        $expectedGalleryId = $expectedGallery->getId();
        $expectedStatusCode = 200;


        // when
        $this->httpClient->request('GET', '/gallery/' .strval($expectedGalleryId));
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $this->galleryService->delete($expectedGallery);

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test delete route for admin user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testDeleteRouteAdminUser(): void
    {
        // given
        $this->removeUser();
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);
        $expectedGallery = new Gallery();
        $expectedGallery->setTitle('Test Gallery Edited');
        $this->galleryService->save($expectedGallery);
        $expectedGalleryId = $expectedGallery->getId();

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', '/gallery/' .strval($expectedGalleryId) .'/delete');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $this->httpClient->submitForm('Usuń');

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test delete route for normal user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testDeleteRouteNormalUser(): void
    {
        // given
        $this->removeUser();
        $expectedStatusCode = 200;
        $user = $this->createUser([UserRole::ROLE_USER->value]);
        $this->httpClient->loginUser($user);
        $expectedGallery = new Gallery();
        $expectedGallery->setTitle('Test Gallery Edited');
        $this->galleryService->save($expectedGallery);
        $expectedGalleryId = $expectedGallery->getId();

        // when
        $this->httpClient->followRedirects(true);
        $this->httpClient->request('GET', '/gallery/' .strval($expectedGalleryId) .'/delete');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $this->galleryService->delete($expectedGallery);

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test delete route for normal user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testDeleteRoutAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 302;
        $expectedGallery = new Gallery();
        $expectedGallery->setTitle('Test Gallery Edited');
        $this->galleryService->save($expectedGallery);
        $expectedGalleryId = $expectedGallery->getId();

        // when
        $this->httpClient->request('GET', '/gallery/' .strval($expectedGalleryId) .'/delete');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $this->galleryService->delete($expectedGallery);

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
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
        $gallery->setTitle('Test Gallery');
        $this->galleryService->save($gallery);
        return $gallery;
    }

}