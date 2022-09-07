<?php
/**
 * UserData fixtures.
 */

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserData;

/**
 * Class UserDataFixtures.
 */
class UserDataFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        $this->createMany(5, 'usersData', function (int $i) {
            $usersData = new UserData();
            $usersData->setLogin($this->faker->unique()->word);
            $usersData->setFirstname($this->faker->unique()->word);

            return $usersData;
        });

        $this->createMany(5, 'usersDataAdmin', function (int $i) {
            $usersData = new UserData();
            $usersData->setLogin($this->faker->unique()->word);
            $usersData->setFirstname($this->faker->unique()->word);

            return $usersData;
        });

        $this->manager->flush();
    }
}
