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
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Title.
     *
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 45)]
    private ?string $title = null;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(mappedBy: 'gallery', targetEntity: Photo::class)]
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
        return $this->photos;
    }

    /**
     * Add photo.
     *
     * @param Photo $photo Photo entity
     */
    public function addPhoto(Photo $photo): void
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setGallery($this);
        }
    }

    /**
     * Remove photo.
     *
     * @param Photo $photo Photo entity
     */
    public function removePhoto(Photo $photo): void
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getGallery() === $this) {
                $photo->setGallery(null);
            }
        }
    }
}
