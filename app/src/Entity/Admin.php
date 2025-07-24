<?php

/**
 * Admin Entity.
 */

namespace App\Entity;

use App\Repository\AdminRepository;
use Deprecated;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Reprezentuje administratora systemu.
 */
#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class Admin implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> Role użytkownika
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string|null Zaszyfrowane hasło użytkownika
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * Pobiera identyfikator administratora.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Pobiera adres email.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Ustawia adres email.
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Pobiera identyfikator użytkownika (email).
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Pobiera role użytkownika.
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Ustawia role użytkownika.
     *
     * @param string[] $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Pobiera zaszyfrowane hasło.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Ustawia zaszyfrowane hasło.
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Usuwa dane tymczasowe (zgodnie z interfejsem UserInterface).
     *
     * @deprecated usunięcie planowane przy aktualizacji do Symfony 8
     */
    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // Do nothing
    }
}
