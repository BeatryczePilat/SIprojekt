<?php

/**
 * Url Entity.
 */

namespace App\Entity;

use App\Repository\UrlRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Reprezentuje encję Url, przechowującą informacje o skróconych adresach URL.
 */
#[ORM\Entity(repositoryClass: UrlRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Url
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $originalURL = null;

    #[ORM\Column(length: 255)]
    private ?string $shortCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column]
    private ?int $clicks = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $updatedAt = null;

    /**
     * @var Tag|null
     */
    #[ORM\ManyToOne(targetEntity: Tag::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Tag $tag = null;

    /**
     * Pobiera unikalny identyfikator adresu URL.
     *
     * @return int|null Identyfikator adresu URL lub null, jeśli nie ustawiono
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Pobiera oryginalny adres URL.
     *
     * @return string|null Oryginalny adres URL lub null, jeśli nie ustawiono
     */
    public function getOriginalURL(): ?string
    {
        return $this->originalURL;
    }

    /**
     * Ustawia oryginalny adres URL.
     *
     * @param string $originalURL Oryginalny adres URL do ustawienia
     */
    public function setOriginalURL(string $originalURL): static
    {
        $this->originalURL = $originalURL;

        return $this;
    }

    /**
     * Pobiera krótki kod adresu URL.
     *
     * @return string|null Krótki kod adresu URL lub null, jeśli nie ustawiono
     */
    public function getShortCode(): ?string
    {
        return $this->shortCode;
    }

    /**
     * Ustawia krótki kod adresu URL.
     *
     * @param string $shortCode Krótki kod do ustawienia
     */
    public function setShortCode(string $shortCode): static
    {
        $this->shortCode = $shortCode;

        return $this;
    }

    /**
     * Pobiera adres e-mail powiązany z adresem URL.
     *
     * @return string|null Adres e-mail lub null, jeśli nie ustawiono
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Ustawia adres e-mail powiązany z adresem URL.
     *
     * @param string|null $email Adres e-mail do ustawienia lub null
     */
    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Pobiera liczbę kliknięć w adres URL.
     *
     * @return int|null Liczba kliknięć lub null, jeśli nie ustawiono
     */
    public function getClicks(): ?int
    {
        return $this->clicks;
    }

    /**
     * Ustawia liczbę kliknięć w adres URL.
     *
     * @param int $clicks Liczba kliknięć do ustawienia
     */
    public function setClicks(int $clicks): static
    {
        $this->clicks = $clicks;

        return $this;
    }

    /**
     * Pobiera datę utworzenia adresu URL.
     *
     * @return DateTimeInterface|null Data utworzenia lub null, jeśli nie ustawiono
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Ustawia datę utworzenia adresu URL.
     *
     * @param DateTimeInterface $createdAt Data utworzenia do ustawienia
     */
    public function setCreatedAt(DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Pobiera datę ostatniej aktualizacji adresu URL.
     *
     * @return DateTimeInterface|null Data aktualizacji lub null, jeśli nie ustawiono
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Ustawia datę ostatniej aktualizacji adresu URL.
     *
     * @param DateTimeInterface $updatedAt Data aktualizacji do ustawienia
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Pobiera kolekcję tagów powiązanych z adresem URL.
     *
     * @return Tag|null
     */
    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    /**
     * @param Tag|null $tag
     * @return $this
     */
    public function setTag(?Tag $tag): static
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * Automatycznie ustawia daty przed zapisem do bazy.
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new DateTimeImmutable();

        if ($this->createdAt === null) {
            $this->createdAt = new DateTimeImmutable();
        }
    }
}
