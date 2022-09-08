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
     *
     * @var \App\Repository\UserDataRepository
     */
    private $userDataRepository;

    /**
     * Paginator.
     *
     * @var \Knp\Component\Pager\PaginatorInterface
     */
    private $paginator;

    /**
     * UserDataService constructor.
     *
     * @param \App\Repository\UserDataRepository      $userDataRepository UserData repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator          Paginator
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
     * @return \Knp\Component\Pager\Pagination\PaginationInterface Paginated list
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
     * @param \App\Entity\UserData $userData UserData entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(UserData $userData): void
    {
        $this->userDataRepository->save($userData);
    }
}