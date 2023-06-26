<?php
/**
 * Tag
Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\Tag
    ;
use App\Entity\User;
use App\Entity\UserData;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class TagControllerTest.
 */
class TagControllerTest extends WebTestCase
{
    /**
     * Test client.
     */
    private KernelBrowser $httpClient;

    private ?EntityManagerInterface $entityManager;

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test index route for anonymous user.
     */
    public function testIndexRouteAnonymousUser(): void
    {
        // given
        $expectedStatusCode = 404;

        // when
        $this->httpClient->request('GET', '/tag
');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route for admin user.
     */
    public function testIndexRouteAdminUser(): void
    {
        $expectedStatusCode = 200;
        $this->removeUser();
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        // when
        $this->httpClient->request('GET', '/tag');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    public function testDeleteTagAdmin(): void
    {
        $expectedStatusCode = 200;
        $this->removeUser();
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $this->httpClient->loginUser($adminUser);

        $tagRepository =
            static::getContainer()->get(TagRepository::class);
        $testTag = new Tag();
        $testTag->setTitle('TestTagCreated');
        $tagRepository->save($testTag);
        $testTagId = $testTag->getId();

        $this->httpClient->request('GET', '/tag/' . $testTagId . '/delete');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
        $this->removeUser();

    }

    public function testDeleteTagAnonymousUser(): void
    {
        $expectedStatusCode = 302;
//        $this->removeUser();
//        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
//        $this->httpClient->loginUser($adminUser);

        $tagRepository =
            static::getContainer()->get(TagRepository::class);
        $testTag = new Tag();
        $testTag->setTitle('TestTagCreated');
        $tagRepository->save($testTag);
        $testTagId = $testTag->getId();

        $this->httpClient->request('GET', '/tag/' . $testTagId . '/delete');
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();
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

}