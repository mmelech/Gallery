<?php

namespace App\Repository;

use App\Entity\Gallery;
use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Photo>
 *
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select('photo', 'gallery')
            ->join('photo.gallery', 'gallery')
            ->orderBy('photo.date', 'DESC');
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('photo');
    }

    /**
     * Count photos by gallery.
     *
     * @param Gallery $gallery Gallery
     *
     * @return int Number of photos in gallery
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByGallery(Gallery $gallery): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('photo.id'))
            ->where('photo.gallery = :gallery')
            ->setParameter(':gallery', $gallery)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function add(Photo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /***
    * public function remove(Post $entity, bool $flush = false): void
    * {
        * $this->getEntityManager()->remove($entity);
 *
* if ($flush) {
            * $this->getEntityManager()->flush();
        * }
    * }
 *
* /**
     * Save entity.
     *
     * @param Photo $Photo Photo entity
     */
    public function save(Photo $photo): void
    {
        $this->_em->persist($photo);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Photo $photo Photo entity
     */
    public function delete(Photo $photo): void
    {
        $this->_em->remove($photo);
        $this->_em->flush();
    }
}
