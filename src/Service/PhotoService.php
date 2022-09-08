<?php
/**
 * Photo service.
 */

namespace App\Service;

use App\Entity\Photo;
use App\Entity\User;
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
     * Gallery service.
     */
    private GalleryServiceInterface $galleryService;

    /**
     * Tag service.
     */
    private TagServiceInterface $tagService;

    /**
     * Tag repository.
     */
    private TagRepository $tagRepository;

    /**
     * Constructor.
     *
     * @param GalleryServiceInterface $galleryService Gallery service
     * @param PhotoRepository     $photoRepository Photo repository
     * @param PaginatorInterface $paginator      Paginator
     * @param TagServiceInterface      $tagService      Tag service
     */
    public function __construct(PhotoRepository $photoRepository, PaginatorInterface $paginator,
                                TagServiceInterface $tagService,GalleryServiceInterface $galleryService)
    {
        $this->photoRepository = $photoRepository;
        $this->paginator = $paginator;
        $this->tagService = $tagService;
        $this->galleryService = $galleryService;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     * @param array<string, int> $filters Filters array
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, array $filters = []): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);
        return $this->paginator->paginate(
            $this->photoRepository->queryAll($filters),
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

    /**
     * Prepare filters for the tags list.
     *
     * @param array<string, int> $filters Raw filters from request
     *
     * @return array<string, object> Result array of filters
     */
    public function prepareFilters(array $filters): array
    {
        $resultFilters = [];
        if (!empty($filters['gallery_id'])) {
            $gallery = $this->galleryService->findOneById($filters['gallery_id']);
            if (null !== $gallery) {
                $resultFilters['gallery'] = $gallery;
            }
        }

        if (!empty($filters['tag_id'])) {
            $tag = $this->tagService->findOneById($filters['tag_id']);
            if (null !== $tag) {
                $resultFilters['tag'] = $tag;
            }
        }

        return $resultFilters;
    }
}
