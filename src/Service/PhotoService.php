<?php
/**
 * Photo service.
 */

namespace App\Service;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class PhotoService.
 */
class PhotoService implements PhotoServiceInterface
{
    /**
     * Photo repository.
     */
    private PhotoRepository $photoRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param PhotoRepository     $photoRepository Photo repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(PhotoRepository $photoRepository, PaginatorInterface $paginator)
    {
        $this->photoRepository = $photoRepository;
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
            $this->photoRepository->queryAll(),
            $page,
            PhotoRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Photo $photo Photo entity
     */
    public function save(Photo $photo): void
    {
        $this->photoRepository->save($photo);
    }

    /**
     * Delete entity.
     *
     * @param Photo $photo Photo entity
     */
    public function delete(Photo $photo): void
    {
        $this->photoRepository->delete($photo);
    }
}
