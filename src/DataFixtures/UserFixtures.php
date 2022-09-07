<?php
/**
 * User fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserFixtures.
 */
class UserFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Password hasher.
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Load data.
     */
    protected function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(5, 'users', function (int $i) {
            $user = new User();
            $user->setEmail(sprintf('user%d@example.com', $i));
            $user->setRoles([UserRole::ROLE_USER->value]);
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'user1234'
                )
            );

            $user->setUserData($this->getReference('usersData_'.$i));

            return $user;
        });

        $this->createMany(5, 'admins', function (int $i) {
            $user = new User();
            $user->setEmail(sprintf('admin%d@example.com', $i));
            $user->setRoles([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'admin1234'
                )
            );

            $user->setUserData($this->getReference('usersDataAdmin_'.$i));

            return $user;
        });

        $this->manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [UserDataFixtures::class];
    }
}
