<?php
/**
 * Comment entity.
 */

namespace App\Entity;

use App\Repository\CommentRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class Comment.
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'comments')]
class Comment
{
    /**
     * Primary key.
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Content.
     *
     * @var string|null
     */
    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'create')]
    private ?DateTimeImmutable $date;

    /**
     * Photo.
     *
     * @var Photo|null
     */
    #[ORM\ManyToOne(targetEntity: Photo::class, inversedBy: 'comments')]
    private $photo;

    /**
     * Author.
     *
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[Assert\Type(User::class)]
    private ?User $author;

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for content.
     *
     * @return string|null Content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Setter for content.
     *
     * @param string $content Content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Getter for date.
     */
    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * Setter for date.
     */
    public function setDate(DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    /**
     * @return Photo|null
     */
    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    /**
     * Setter for Photo.
     *
     * @param Photo|null $photo
     * @return $this
     */
    public function setPhoto(?Photo $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
