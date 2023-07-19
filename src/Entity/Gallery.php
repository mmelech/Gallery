<?php
/**
 * Gallery entity.
 */

namespace App\Entity;

use App\Repository\GalleryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Gallery.
 */
#[ORM\Entity(repositoryClass: GalleryRepository::class)]
#[ORM\Table(name: 'galleries')]
#[ORM\UniqueConstraint(name: 'uq_galleries_title', columns: ['title'])]
#[UniqueEntity(fields: ['title'])]
class Gallery
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Title.
     */
    #[ORM\Column(type: 'string', length: 45)]
    private ?string $title = null;

    /**
     * Photo.
     */
    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'gallery', fetch: 'EXTRA_LAZY')]
    private $photos;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

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
     * Getter for title.
     *
     * @return string|null Title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for title.
     *
     * @param string|null $title Title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Getter for photo.
     *
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        if (null === $this->photos) {
            $this->photos = new ArrayCollection();
        }

        return $this->photos;
    }
}
