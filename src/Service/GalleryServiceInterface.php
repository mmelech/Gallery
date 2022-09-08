<?php
/**
 * Gallery service interface.
 */

namespace App\Service;

use App\Entity\Gallery;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface GalleryInterface.
 */
interface GalleryServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Gallery $gallery Gallery entity
     */
    public function save(Gallery $gallery): void;

    /**
     * Delete entity.
     *
     * @param Gallery $gallery Gallery entity
     */
    public function delete(Gallery $gallery): void;
}
