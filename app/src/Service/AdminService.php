<?php

/**
 * Admin Service.
 */

namespace App\Service;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Serwis do obsługi logiki biznesowej administratora, w tym aktualizacji danych i haseł.
 */
class AdminService
{
    private AdminRepository $adminRepository;
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @param AdminRepository             $adminRepository repozytorium
     *                                                     administratorów
     * @param UserPasswordHasherInterface $passwordHasher  hasher do obsługi
     *                                                     haseł
     */
    public function __construct(AdminRepository $adminRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->adminRepository = $adminRepository;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Aktualizacja danych administratora (email i opcjonalnie hasło).
     *
     * @param Admin       $admin         admin do aktualizacji
     * @param string|null $plainPassword nowe hasło lub null
     */
    public function updateProfile(Admin $admin, ?string $plainPassword = null): void
    {
        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($admin, $plainPassword);
            $admin->setPassword($hashedPassword);
        }

        $this->adminRepository->save($admin);
    }

    /**
     * Zmiana hasła bez zmiany pozostałych danych.
     *
     * @param Admin  $admin            admin, którego hasło ma zostać zmienione
     * @param string $newPlainPassword nowe hasło w postaci jawnej
     */
    public function changePassword(Admin $admin, string $newPlainPassword): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($admin, $newPlainPassword);
        $admin->setPassword($hashedPassword);
        $this->adminRepository->save($admin);
    }

    /**
     * Zmiana hasła z weryfikacją obecnego hasła.
     *
     * @param Admin  $admin                admin, którego hasło ma zostać zmienione
     * @param string $currentPlainPassword obecne hasło w postaci jawnej
     * @param string $newPlainPassword     nowe hasło w postaci jawnej
     *
     * @return bool true jeśli hasło zmienione, false jeśli obecne hasło nieprawidłowe
     */
    public function changePasswordWithVerification(Admin $admin, string $currentPlainPassword, string $newPlainPassword): bool
    {
        if (!$newPlainPassword) {
            return true;
        }

        if (!$this->passwordHasher->isPasswordValid($admin, $currentPlainPassword)) {
            return false;
        }

        $hashedPassword = $this->passwordHasher->hashPassword($admin, $newPlainPassword);
        $admin->setPassword($hashedPassword);
        $this->adminRepository->save($admin);

        return true;
    }

    /**
     * Aktualizacja profilu z weryfikacją hasła, jeśli podano nowe.
     *
     * @param Admin       $admin           obiekt administratora
     * @param string|null $currentPassword obecne hasło (do weryfikacji)
     * @param string|null $newPassword     nowe hasło lub null
     *
     * @return bool true jeśli zapis zakończony, false jeśli hasło nieprawidłowe
     */
    public function updateProfileWithPasswordVerification(Admin $admin, ?string $currentPassword, ?string $newPassword): bool
    {
        if ($newPassword) {
            if (!$this->passwordHasher->isPasswordValid($admin, $currentPassword)) {
                return false;
            }

            $hashedPassword = $this->passwordHasher->hashPassword($admin, $newPassword);
            $admin->setPassword($hashedPassword);
        }

        $this->adminRepository->save($admin);

        return true;
    }

    /**
     * Zapis profilu administratora bez zmiany hasła (np. zmiana e-mail).
     *
     * @param Admin $admin obiekt z nowym e‑mailem
     */
    public function saveProfile(Admin $admin): void
    {
        $this->adminRepository->save($admin);
    }
}
