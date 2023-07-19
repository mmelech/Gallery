<?php
/**
 * Photo fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\Tag;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class PhotoFixtures.
 */
class PhotoFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(30, 'photos', function (int $i) {
            $photo = new Photo();
            $photo->setTitle($this->faker->sentence);
            $photo->setContent($this->faker->paragraph);
            $photo->setDate(
                DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );

            /** @var Gallery $gallery */
            $gallery = $this->getRandomReference('galleries');
            $photo->setGallery($gallery);

            /** @var array<array-key, Tag> $tags */
            $tags = $this->getRandomReferences(
                'tags',
                $this->faker->numberBetween(0, 5)
            );

            /** @var User $author */
            $author = $this->getRandomReference('users');
            $photo->setAuthor($author);

            return $photo;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array Array of dependencies
     *
     * @psalm-return array{0: GalleryFixtures::class}
     */
    public function getDependencies(): array
    {
        return [GalleryFixtures::class, TagFixtures::class, UserFixtures::class];
    }
}
