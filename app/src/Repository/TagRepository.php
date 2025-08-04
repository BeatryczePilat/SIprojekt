<?php

/**
 * Tag Repository.
 **/

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 */
class TagRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * Zapisuje tag do bazy danych.
     *
     * @param Tag  $entity Obiekt tagu do zapisania
     * @param bool $flush  Czy natychmiast wykonać flush (domyślnie true)
     */
    public function save(Tag $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Usuwa tag z bazy danych.
     *
     * @param Tag  $entity Obiekt tagu do usunięcia
     * @param bool $flush  Czy natychmiast wykonać flush (domyślnie true)
     */
    public function remove(Tag $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Zwraca łączną liczbę tagów w bazie.
     *
     * @return int Liczba rekordów w tabeli tag
     */
    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Zwraca liczbę powiązanych URL-i dla każdego tagu.
     *
     * Przydatne do statystyk, raportów lub dashboardów.
     *
     * @return array<int, array{name: string, count: int}> Lista tagów z nazwą i liczbą powiązanych URL-i
     */
    public function getTagsWithUrlCounts(): array
    {
        return $this->createQueryBuilder('t')
            ->select('t.name AS name, COUNT(u.id) AS count')
            ->leftJoin('t.urls', 'u')
            ->groupBy('t.id')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Tag[] Returns an array of Tag objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tag
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
