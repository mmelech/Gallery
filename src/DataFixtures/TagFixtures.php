<?php
/**
 * Tag fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Tag;

/**
 * Class TagFixtures.
 */
class TagFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        $this->createMany(30, 'tags', function (int $i) {
            $tag = new Tag();
            $tag->setTitle($this->faker->word);

            return $tag;
        });

        $this->manager->flush();
    }
}
