<?php

/**
 * Admin Service.
 */

namespace App\Service;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Serwis do obsługi logiki biznesowej administratora,
 * w tym zarządzania profilem oraz zmianą hasła.
 */
class AdminService
{
    /**
     * Konstruktor z wstrzykiwaniem zależności (DI).
     *
     * @param AdminRepository             $adminRepository Repozytorium administratorów
     * @param UserPasswordHasherInterface $passwordHasher  Serwis do haszowania haseł
     */
    public function __construct(private readonly AdminRepository $adminRepository, private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Aktualizuje dane administratora, opcjonalnie zmieniając hasło.
     *
     * @param Admin       $admin         Obiekt administratora
     * @param string|null $plainPassword Nowe hasło (jeśli podane)
     */
    public function updateProfile(Admin $admin, ?string $plainPassword = null): void
    {
        if ($plainPassword) {
            $admin->setPassword(
                $this->passwordHasher->hashPassword($admin, $plainPassword)
            );
        }

        $this->adminRepository->save($admin);
    }

    /**
     * Zmienia hasło administratora (bez weryfikacji aktualnego).
     *
     * @param Admin  $admin            Administrator
     * @param string $newPlainPassword Nowe hasło
     */
    public function changePassword(Admin $admin, string $newPlainPassword): void
    {
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, $newPlainPassword)
        );

        $this->adminRepository->save($admin);
    }

    /**
     * Zmienia hasło po sprawdzeniu poprawności obecnego hasła.
     *
     * @param Admin  $admin                Administrator
     * @param string $currentPlainPassword Obecne hasło (do weryfikacji)
     * @param string $newPlainPassword     Nowe hasło
     *
     * @return bool True jeśli zmiana udana, false jeśli hasło niepoprawne
     */
    public function changePasswordWithVerification(Admin $admin, string $currentPlainPassword, string $newPlainPassword): bool
    {
        if (!$newPlainPassword) {
            return true;
        }

        if (!$this->passwordHasher->isPasswordValid($admin, $currentPlainPassword)) {
            return false;
        }

        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, $newPlainPassword)
        );

        $this->adminRepository->save($admin);

        return true;
    }

    /**
     * Aktualizuje profil z opcjonalną zmianą hasła po weryfikacji.
     *
     * @param Admin       $admin           Administrator
     * @param string|null $currentPassword Obecne hasło
     * @param string|null $newPassword     Nowe hasło
     *
     * @return bool True jeśli zmieniono, false jeśli hasło niepoprawne
     */
    public function updateProfileWithPasswordVerification(Admin $admin, ?string $currentPassword, ?string $newPassword): bool
    {
        if ($newPassword) {
            if (!$this->passwordHasher->isPasswordValid($admin, $currentPassword)) {
                return false;
            }

            $admin->setPassword(
                $this->passwordHasher->hashPassword($admin, $newPassword)
            );
        }

        $this->adminRepository->save($admin);

        return true;
    }

    /**
     * Zapisuje zmiany profilu bez zmiany hasła.
     *
     * @param Admin $admin Admin $admin
     */
    public function saveProfile(Admin $admin): void
    {
        $this->adminRepository->save($admin);
    }
}
