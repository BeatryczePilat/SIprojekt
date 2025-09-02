<?php

/**
 * Admin Entity.
 */

namespace App\Entity;

use App\Entity\Enum\UserRole;
use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reprezentuje administratora systemu.
 */
#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class Admin implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * ID.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Email.
     */
    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'admin.email.not_blank')]
    #[Assert\Email(message: 'admin.email.invalid')]
    private ?string $email = null;

    /**
     * Role użytkownika.
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * Zaszyfrowane hasło użytkownika.
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: 'admin.password.not_blank')]
    private ?string $password = null;

    /**
     * Pobiera identyfikator administratora.
     *
     * @return int|null int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Pobiera adres email.
     *
     * @return string|null string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Ustawia adres email.
     *
     * @param string $email string
     *
     * @return $this this
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Pobiera identyfikator użytkownika (email).
     *
     * @return string string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Pobiera role użytkownika.
     *
     * @return array|string[] array|string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = UserRole::ROLE_USER->value;

        return array_unique($roles);
    }

    /**
     * Ustawia role użytkownika.
     *
     * @param array $roles array
     *
     * @return void void
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Pobiera zaszyfrowane hasło.
     *
     * @return string|null string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Ustawia zaszyfrowane hasło.
     *
     * @param string $password string
     *
     * @return $this this
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Nickname (pseudonim) administratora.
     *
     * @var string|null string|null
     */
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'admin.nickname.min_length',
        maxMessage: 'admin.nickname.max_length'
    )]
    private ?string $nickname = null;

    /**
     * Zwraca pseudonim administratora.
     *
     * @return string|null string|null
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * Ustawia pseudonim administratora.
     *
     * @param string|null $nickname string|null
     *
     * @return $this this
     */
    public function setNickname(?string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Usuwa dane tymczasowe (zgodnie z interfejsem UserInterface).
     *
     * @return void void
     */
    public function eraseCredentials(): void
    {
        // Do nothing
    }
}
