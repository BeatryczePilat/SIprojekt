<?php

/**
 * Tag Entity.
 */

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Reprezentuje encję Tag, używaną do kategoryzowania adresów URL.
 */
#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
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
     * Nazwa.
     *
     * @var string|null string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * Slug.
     *
     * @var string|null string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * Pobiera unikalny identyfikator tagu.
     *
     * @return int|null int|null
     * Identyfikator tagu lub null, jeśli nie ustawiono.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Pobiera nazwę tagu.
     *
     * @return string|null string|null
     * Nazwa tagu lub null, jeśli nie ustawiono.
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
     * Slug tagu lub null, jeśli nie ustawiono.
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
