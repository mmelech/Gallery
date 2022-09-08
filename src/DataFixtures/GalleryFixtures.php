<?php
/**
 * Gallery fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Gallery;

/**
 * Class GalleryFixtures.
 */
class GalleryFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        $this->createMany(20, 'galleries', function (int $i) {
            $gallery = new Gallery();
            $gallery->setTitle($this->faker->unique()->word);

            return $gallery;
        });

        $this->manager->flush();
    }
}
