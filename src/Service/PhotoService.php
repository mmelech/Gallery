<?php
/**
 * Photo service.
 */

namespace App\Service;

use App\Entity\Photo;
use App\Entity\User;
use App\Repository\PhotoRepository;
use App\Repository\TagRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class PhotoService.
 */
class PhotoService implements PhotoServiceInterface
{
    /**
     * Target directory.
     */
    private string $targetDirectory;

    /**
     * Photo repository.
     */
    private PhotoRepository $photoRepository;

    /**
     * File upload service.
     */
    private FileUploadServiceInterface $fileUploadService;

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
     * File system service.
     */
    private Filesystem $filesystem;

    /**
     * Constructor.
     *
     * @param GalleryServiceInterface    $galleryService    Gallery service
     * @param PhotoRepository            $photoRepository   Photo repository
     * @param PaginatorInterface         $paginator         Paginator
     * @param TagServiceInterface        $tagService        Tag service
     * @param FileUploadServiceInterface $fileUploadService File Upload service
     * @param Filesystem                 $filesystem        Filesystem component
     */
    public function __construct(
        string $targetDirectory,
        PhotoRepository $photoRepository,
        PaginatorInterface $paginator,
        TagServiceInterface $tagService,
        GalleryServiceInterface $galleryService,
        FileUploadServiceInterface $fileUploadService,
        Filesystem $filesystem
    )
    {
        $this->targetDirectory = $targetDirectory;
        $this->photoRepository = $photoRepository;
        $this->paginator = $paginator;
        $this->tagService = $tagService;
        $this->galleryService = $galleryService;
        $this->fileUploadService = $fileUploadService;
        $this->filesystem = $filesystem;
    }

    /**
     * Get paginated list.
     *
     * @param int                $page    Page number
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

    /**
     * Create photo.
     *
     * @param UploadedFile  $uploadedFile Uploaded file
     * @param Photo         $photo        Photo entity
     * @param UserInterface $user         User interface
     */
    public function create(UploadedFile $uploadedFile, Photo $photo, UserInterface $user): void
    {
        $photoFilename = $this->fileUploadService->upload($uploadedFile);

        $photo->setAuthor($user);
        $photo->setFilename($photoFilename);
        $this->photoRepository->save($photo);
    }

    /**
     * Update photo.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Photo        $photo        Photo entity
     * @param User         $user         User entity
     */
    public function update(UploadedFile $uploadedFile, Photo $photo, UserInterface $user): void
    {
        $filename = $photo->getFilename();

        if (null !== $filename) {
            $this->filesystem->remove(
                $this->targetDirectory.'/'.$filename
            );

            $this->create($uploadedFile, $photo, $user);
        }
    }
}
