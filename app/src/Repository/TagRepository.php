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
     * Zapis tagu do bazy.
     *
     * @param Tag  $entity tag do zapisania
     * @param bool $flush  czy natychmiast zapisać
     */
    public function save(Tag $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Usuwanie tagu z bazy.
     *
     * @param Tag  $entity tag do usunięcia
     * @param bool $flush  czy natychmiast usunąć
     */
    public function remove(Tag $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Zwracanie liczby wszystkich tagów.
     *
     * @return int liczba tagów
     */
    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Do statystyk – liczba url dla każdego tagu.
     *
     * @return array lista tagów z liczbą powiązanych url
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
