<?php
/**
 * UserData service.
 */

namespace App\Service;

use App\Entity\UserData;
use App\Repository\UserDataRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class UserDataService.
 */
class UserDataService
{
    /**
     * UserData repository.
     */
    private UserDataRepository $userDataRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param UserDataRepository $userDataRepository UserData repository
     * @param PaginatorInterface $paginator          Paginator
     */
    public function __construct(UserDataRepository $userDataRepository, PaginatorInterface $paginator)
    {
        $this->userDataRepository = $userDataRepository;
        $this->paginator = $paginator;
    }

    /**
     * Create paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function createPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userDataRepository->queryAll(),
            $page,
            UserDataRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save userData.
     *
     * @param UserData $userData UserData entity
     */
    public function save(UserData $userData): void
    {
        $this->userDataRepository->save($userData);
    }
}
