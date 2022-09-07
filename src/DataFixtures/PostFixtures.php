<?php
/**
 * Post fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use App\DataFixtures\AbstractBaseFixtures;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class PostFixtures.
 */
class PostFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(30, 'posts', function (int $i) {
            $post = new Post();
            $post->setTitle($this->faker->sentence);
            $post->setContent($this->faker->paragraph);
            $post->setDate(
                DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days')));

            /** @var Category $category */
            $category = $this->getRandomReference('categories');
            $post->setCategory($category);

            /** @var array<array-key, Tag> $tags */
            $tags = $this->getRandomReferences(
                'tags',
                $this->faker->numberBetween(0, 5));

            return $post;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: CategoryFixtures::class}
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class, TagFixtures::class, UserFixtures::class];
    }
}
