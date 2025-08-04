<?php

/**
 * Admin Repository.
 **/

namespace App\Repository;

use App\Entity\Admin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * Klasa repozytorium dla encji Admin.
 *
 * @extends ServiceEntityRepository<Admin>
 */
class AdminRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * Konstruktor repozytorium Admin.
     *
     * @param ManagerRegistry $registry Rejestr zarządzający encjami
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Admin::class);
    }

    /**
     * Automatyczna aktualizacja (rehash) hasła – wywoływana np. przez listener hasła Symfony.
     *
     * @param PasswordAuthenticatedUserInterface $user              Użytkownik, którego hasło trzeba zaktualizować
     * @param string                             $newHashedPassword Nowe, zaszyfrowane hasło
     *
     * @throws UnsupportedUserException Jeśli użytkownik nie jest typu Admin
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Admin) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Zapisuje encję administratora w bazie danych.
     *
     * @param Admin $entity Obiekt Admin do zapisania
     * @param bool  $flush  Czy natychmiast wykonać flush (domyślnie: true)
     */
    public function save(Admin $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Admin[] Returns an array of Admin objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Admin
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
