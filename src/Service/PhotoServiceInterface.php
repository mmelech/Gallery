<?php
/**
 * Photo service interface.
 */

namespace App\Service;

use App\Entity\Photo;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface PhotoServiceInterface.
 */
interface PhotoServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int   $page    Page number
     * @param array $filters Filters
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, array $filters = []): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Photo $photo Photo entity
     */
    public function save(Photo $photo): void;

    /**
     * Create photo.
     *
     * @param UploadedFile  $uploadedFile Uploaded file
     * @param Photo         $photo        Photo entity
     * @param UserInterface $user         User interface
     */
    public function create(UploadedFile $uploadedFile, Photo $photo, UserInterface $user): void;

    /**
     * Delete entity.
     *
     * @param Photo $photo Photo entity
     */
    public function delete(Photo $photo): void;

    /**
     * Prepare filters.
     *
     * @param array $filters Filters
     */
    public function prepareFilters(array $filters): array;

    /**
     * Update avatar.
     *
     * @param UploadedFile  $uploadedFile Uploaded file
     * @param Photo         $photo        Photo entity
     * @param UserInterface $user         User interface
     */
    public function update(UploadedFile $uploadedFile, Photo $photo, UserInterface $user): void;
}
