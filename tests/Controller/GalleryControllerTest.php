<?php
/**
 * Hello controller tests.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;

/**
 * Class HelloControllerTest.
 */
class GalleryControllerTest extends WebTestCase
{
    /**
     * Test '/gallery' route.
     */
    public function testGalleryRoute(): void
    {
        $expectedStatusCode = 200;
        $client = static::createClient();
        $client->request('GET', '/gallery');
        $resultHttpStatusCode = $client->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultHttpStatusCode);
    }

    /**
     * Test '/gallery' route.
     */
    public function testGalleryRouteUser(): void
    {
        $expectedStatusCode = 200;
//        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $user = new User();
        $user->setEmail('userX@email.com');

////        $user->setUpdatedAt(new DateTime('now'));
////        $user->setCreatedAt(new DateTime('now'));
//        $user->setRoles(['ROLE_USER']);
//        $user->setPassword(
//            $passwordHasher->hashPassword(
//                $user,
//                'P@ssw0rd'
//            )
//        );
        $client = static::createClient();
        $client->request('GET', '/gallery');
        $resultHttpStatusCode = $client->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultHttpStatusCode);
    }
}
