<?php

/**
 * Url Repository.
 */

namespace App\Repository;

use App\Entity\Url;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Url>
 *
 * Repozytorium do operacji na encji Url.
 */
class UrlRepository extends ServiceEntityRepository
{
    /**
     * Konstruktor repozytorium Url.
     *
     * @param ManagerRegistry $registry Rejestr Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Url::class);
    }

    /**
     * Zapis lub aktualizacja encji w bazie danych.
     *
     * @param Url  $entity url do zapisania
     * @param bool $flush  czy natychmiast wykonać zapis
     */
    public function save(Url $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Usuwanie encji z bazy danych.
     *
     * @param Url  $entity url do usunięcia
     * @param bool $flush  czy natychmiast wykonać operację
     */
    public function remove(Url $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Zwracanie zapytania do pobierania najnowszych adresów url.
     *
     * @return \Doctrine\ORM\Query zapytanie do pobrania danych
     */
    public function findLatestQuery(): \Doctrine\ORM\Query
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery();
    }

    /**
     * Zwracanie adresów url przypisanych do tagu o podanym slug.
     *
     * @param string $slug slug tagu
     *
     * @return Url[] lista adresów
     */
    public function findByTagSlug(string $slug): array
    {
        return $this->createQueryBuilder('u')
            ->join('u.tags', 't')
            ->where('t.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Zwracanie zapytania do linków posortowanych po liczbie kliknięć malejąco.
     *
     * @return \Doctrine\ORM\Query zapytanie do pobrania danych
     */
    public function findMostClickedQuery(): \Doctrine\ORM\Query
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.clicks', 'DESC')
            ->getQuery();
    }

    /**
     * Zwracanie liczby wszystkich skróconych adresów url.
     *
     * @return int liczba rekordów
     */
    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Zwracanie sumy wszystkich kliknięć.
     *
     * @return int łączna liczba kliknięć
     */
    public function sumClicks(): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('SUM(u.clicks)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Zwracanie liczby unikalnych adresów e-mail.
     *
     * @return int liczba unikalnych e-maili
     */
    public function countUniqueEmails(): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(DISTINCT u.email)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Zwracanie adresów url według filtrów.
     *
     * @param array $filters ['email' => ..., 'originalUrl' => ..., 'shortCode' => ..., 'tag' => Tag|null]
     *
     * @return Url[] lista dopasowanych adresów
     */
    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.tags', 't')
            ->addSelect('t');

        if (!empty($filters['email'])) {
            $qb->andWhere('u.email LIKE :email')
                ->setParameter('email', '%'.$filters['email'].'%');
        }

        if (!empty($filters['originalUrl'])) {
            $qb->andWhere('u.originalUrl LIKE :url')
                ->setParameter('url', '%'.$filters['originalUrl'].'%');
        }

        if (!empty($filters['shortCode'])) {
            $qb->andWhere('u.shortCode LIKE :code')
                ->setParameter('code', '%'.$filters['shortCode'].'%');
        }

        if (!empty($filters['tag'])) {
            $qb->andWhere(':tag MEMBER OF u.tags')
                ->setParameter('tag', $filters['tag']);
        }

        return $qb
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Zwracanie listy unikalnych adresów e-mail (dla statystyk).
     *
     * @return string[] tablica e-maili
     */
    public function findUniqueEmails(): array
    {
        $result = $this->createQueryBuilder('u')
            ->select('DISTINCT u.email')
            ->where('u.email IS NOT NULL')
            ->getQuery()
            ->getScalarResult();

        return array_column($result, 'email');
    }

    /**
     * Zwracanie listy tagów z liczbą powiązanych url (dla statystyk).
     *
     * @return array lista tagów z licznikami
     */
    public function findAllTagsWithCounts(): array
    {
        return $this->createQueryBuilder('u')
            ->select('t.name AS name, COUNT(u.id) AS count')
            ->join('u.tags', 't')
            ->groupBy('t.id')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
