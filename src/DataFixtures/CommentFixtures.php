<?php
/**
 * Comment fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class CommentFixtures.
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(30, 'comments', function (int $i) {
            $comment = new Comment();
            $comment->setContent($this->faker->sentence);
            $comment->setDate(
                DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days')));

            /** @var User @author */
            $author = $this->getRandomReference('users');
            $comment->setAuthor($author);

            /** @var Post @post */
            $post = $this->getRandomReference('posts');
            $comment->setPost($post);

            return $comment;
        });

        $this->manager->flush();
    }

    /**
     * @return string[] of dependencies
     *
     */
    public function getDependencies(): array
    {
        return [UserFixtures::class, PostFixtures::class];
    }
}
