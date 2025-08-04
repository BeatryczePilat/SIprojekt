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
    /**
     * ID.
     *
     * @var int|null int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Oryginalny url.
     *
     * @var string|null string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $originalURL = null;

    /**
     * Skrócony url.
     *
     * @var string|null string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $shortCode = null;

    /**
     * Email.
     *
     * @var string|null string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    /**
     * Kliknięcia.
     *
     * @var int|null int|null
     */
    #[ORM\Column]
    private ?int $clicks = null;

    /**
     * Stworzono (data).
     *
     * @var DateTimeInterface|null DateTimeInterface|null
     */
    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $createdAt = null;

    /**
     * Aktualizowano (data).
     *
     * @var DateTimeInterface|null DateTimeInterface|null
     */
    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $updatedAt = null;

    /**
     * Tag.
     *
     * @var Tag|null Tag|null
     */
    #[ORM\ManyToOne(targetEntity: Tag::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Tag $tag = null;

    /**
     * Pobiera unikalny identyfikator adresu URL.
     *
     * @return int|null int|null Identyfikator adresu URL.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Pobiera oryginalny adres URL.
     *
     * @return string|null string|null Oryginalny adres URL.
     */
    public function getOriginalURL(): ?string
    {
        return $this->originalURL;
    }

    /**
     * Ustawia oryginalny adres URL.
     *
     * @param string $originalURL Oryginalny adres URL do ustawienia.
     * @return $this
     */
    public function setOriginalURL(string $originalURL): static
    {
        $this->originalURL = $originalURL;

        return $this;
    }

    /**
     * Pobiera krótki kod adresu URL.
     *
     * @return string|null string|null Krótki kod adresu URL.
     */
    public function getShortCode(): ?string
    {
        return $this->shortCode;
    }

    /**
     *  Ustawia krótki kod adresu URL.
     *
     * @param string $shortCode string $shortCode Krótki kod do ustawienia
     * @return $this $this
     */
    public function setShortCode(string $shortCode): static
    {
        $this->shortCode = $shortCode;

        return $this;
    }

    /**
     * Pobiera adres e-mail powiązany z adresem URL.
     *
     * @return string|null string|null Adres e-mail.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     *  Ustawia adres e-mail powiązany z adresem URL.
     *
     * @param string|null $email string|null $email
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
     * @return int|null int|null Liczba kliknięć.
     */
    public function getClicks(): ?int
    {
        return $this->clicks;
    }

    /**
     * Ustawia liczbę kliknięć w adres URL.
     *
     * @param int $clicks Liczba kliknięć do ustawienia.
     * @return $this
     */
    public function setClicks(int $clicks): static
    {
        $this->clicks = $clicks;

        return $this;
    }

    /**
     * Pobiera datę utworzenia adresu URL.
     *
     * @return DateTimeInterface|null DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Ustawia datę utworzenia adresu URL.
     *
     * @param DateTimeInterface $createdAt DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Pobiera datę ostatniej aktualizacji adresu URL.
     *
     * @return DateTimeInterface|null DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }


    /**
     * Ustawia datę ostatniej aktualizacji adresu URL.
     *
     * @param DateTimeInterface $updatedAt DateTimeInterface $updatedAt
     * @return $this
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): static
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
     * @return $this $this
     */
    public function setTag(?Tag $tag): static
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * Automatycznie ustawia daty przed zapisem do bazy.
     * @return void void
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
