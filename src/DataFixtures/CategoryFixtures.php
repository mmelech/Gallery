<?php
/**
 * Category fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;

/**
 * Class CategoryFixtures.
 */
class CategoryFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        $this->createMany(20, 'categories', function (int $i) {
            $category = new Category();
            $category->setTitle($this->faker->unique()->word);

            return $category;
        });

        $this->manager->flush();
    }
}
