<?php

/**
 * Tag Entity.
 */

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reprezentuje encję Tag, używaną do kategoryzowania adresów URL.
 */
#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    /**
     * ID.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nazwa.
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'tag.name.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'tag.name.max_length')]
    private ?string $name = null;

    /**
     * Slug.
     */
    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Length(max: 255, maxMessage: 'tag.slug.max_length')]
    private ?string $slug = null;

    /**
     * generate.
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    #[ORM\HasLifecycleCallbacks]
    public function generateSlug(): void
    {
        if (!$this->slug && $this->name) {
            $this->slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $this->name));
        }
    }

    /**
     * Pobiera unikalny identyfikator tagu.
     *
     * @return int|null int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Pobiera nazwę tagu.
     *
     * @return string|null string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Ustawia nazwę tagu.
     *
     * @param string $name string $name
     *
     * @return $this $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Pobiera slug tagu.
     *
     * @return string|null string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Ustawia slug tagu.
     *
     * @param string $slug string $slug
     *
     * @return $this $this
     */
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
