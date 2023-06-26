<?php
/**
 * Gallery service.
 */

namespace App\Service;

use App\Entity\Gallery;
use App\Repository\GalleryRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class GalleryService.
 */
class GalleryService implements GalleryServiceInterface
{
    /**
     * Gallery repository.
     */
    private GalleryRepository $galleryRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param GalleryRepository  $galleryRepository Gallery repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(GalleryRepository $galleryRepository, PaginatorInterface $paginator)
    {
        $this->galleryRepository = $galleryRepository;
        $this->paginator = $paginator;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->galleryRepository->queryAll(),
            $page,
            GalleryRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Gallery $gallery Gallery entity
     */
    public function save(Gallery $gallery): void
    {
        $this->galleryRepository->save($gallery);
    }

    /**
     * Delete entity.
     *
     * @param Gallery $gallery Gallery entity
     */
    public function delete(Gallery $gallery): void
    {
        $this->galleryRepository->delete($gallery);
    }

    /**
     * Find by id.
     *
     * @param int $id Gallery id
     *
     * @return Gallery|null Gallery entity
     *
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): ?Gallery
    {
        return $this->galleryRepository->findOneById($id);
    }
}
