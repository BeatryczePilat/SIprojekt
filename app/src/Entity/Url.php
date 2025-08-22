<?php

/**
 * Url Entity.
 */

namespace App\Entity;

use App\Repository\UrlRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Reprezentuje encję Url, przechowującą informacje o skróconych adresach URL.
 */
#[ORM\Entity(repositoryClass: UrlRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Url
{
    /**
     * ID.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Oryginalny url.
     */
    #[ORM\Column(length: 255)]
    private ?string $originalURL = null;

    /**
     * Skrócony url.
     */
    #[ORM\Column(length: 255)]
    private ?string $shortCode = null;

    /**
     * Email.
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    /**
     * Kliknięcia.
     */
    #[ORM\Column]
    private ?int $clicks = null;

    /**
     * Stworzono (data).
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Aktualizowano (data).
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Tag.
     */
    #[ORM\ManyToOne(targetEntity: Tag::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Tag $tag = null;

    /**
     * Pobiera unikalny identyfikator adresu URL.
     *
     * @return int|null int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Pobiera oryginalny adres URL.
     *
     * @return string|null string|null
     */
    public function getOriginalURL(): ?string
    {
        return $this->originalURL;
    }

    /**
     * Ustawia oryginalny adres URL.
     *
     * @param string $originalURL originalURL
     *
     * @return $this this
     */
    public function setOriginalURL(string $originalURL): static
    {
        $this->originalURL = $originalURL;

        return $this;
    }

    /**
     * Pobiera krótki kod adresu URL.
     *
     * @return string|null string|null
     */
    public function getShortCode(): ?string
    {
        return $this->shortCode;
    }

    /**
     *  Ustawia krótki kod adresu URL.
     *
     * @param string $shortCode string
     *
     * @return $this this
     */
    public function setShortCode(string $shortCode): static
    {
        $this->shortCode = $shortCode;

        return $this;
    }

    /**
     * Pobiera adres e-mail powiązany z adresem URL.
     *
     * @return string|null string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     *  Ustawia adres e-mail powiązany z adresem URL.
     *
     * @param string|null $email string|null $email
     *
     * @return $this
     */
    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Pobiera liczbę kliknięć w adres URL.
     *
     * @return int|null int|null Liczba kliknięć
     */
    public function getClicks(): ?int
    {
        return $this->clicks;
    }

    /**
     * Ustawia liczbę kliknięć w adres URL.
     *
     * @param int $clicks Liczba kliknięć do ustawienia
     *
     * @return $this this
     */
    public function setClicks(int $clicks): static
    {
        $this->clicks = $clicks;

        return $this;
    }

    /**
     * Pobiera datę utworzenia adresu URL.
     *
     * @return \DateTimeImmutable|null \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Ustawia datę utworzenia adresu URL.
     *
     * @param \DateTimeImmutable $createdAt \DateTimeImmutable
     *
     * @return $this
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Pobiera datę ostatniej aktualizacji adresu URL.
     *
     * @return \DateTimeImmutable|null \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Ustawia datę ostatniej aktualizacji adresu URL.
     *
     * @param \DateTimeImmutable $updatedAt \DateTimeImmutable
     *
     * @return $this this
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Pobiera kolekcję tagów powiązanych z adresem URL.
     *
     * @return Tag|null Tag|null
     */
    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    /**
     * Ustawianie tagu.
     *
     * @param Tag|null $tag Tag|null $tag
     *
     * @return $this this
     */
    public function setTag(?Tag $tag): static
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Automatycznie ustawia daty przed zapisem do bazy.
     *
     * @return void void
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();

        if (null === $this->createdAt) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }
}
