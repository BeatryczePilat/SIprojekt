<?php

/**
 * Tag Entity.
 */

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Reprezentuje encję Tag, używaną do kategoryzowania adresów URL.
 */
#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Url>
     */
    #[ORM\ManyToMany(targetEntity: Url::class, mappedBy: 'tags')]
    private Collection $urls;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * Inicjalizuje nową instancję encji Tag.
     *
     * Ustawia właściwość urls jako pustą kolekcję ArrayCollection.
     */
    public function __construct()
    {
        $this->urls = new ArrayCollection();
    }

    /**
     * Pobiera unikalny identyfikator tagu.
     *
     * @return int|null Identyfikator tagu lub null, jeśli nie ustawiono
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Pobiera nazwę tagu.
     *
     * @return string|null Nazwa tagu lub null, jeśli nie ustawiono
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Ustawia nazwę tagu.
     *
     * @param string $name Nazwa do ustawienia
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Pobiera kolekcję adresów URL powiązanych z tym tagiem.
     *
     * @return Collection<int, Url>
     */
    public function getUrls(): Collection
    {
        return $this->urls;
    }

    /**
     * Dodaje adres URL do kolekcji tagu.
     *
     * @param Url $url Adres URL do dodania
     */
    public function addUrl(Url $url): static
    {
        if (!$this->urls->contains($url)) {
            $this->urls->add($url);
            $url->addTag($this);
        }

        return $this;
    }

    /**
     * Usuwa adres URL z kolekcji tagu.
     *
     * @param Url $url Adres URL do usunięcia
     */
    public function removeUrl(Url $url): static
    {
        if ($this->urls->removeElement($url)) {
            $url->removeTag($this);
        }

        return $this;
    }

    /**
     * Pobiera slug tagu.
     *
     * @return string|null Slug tagu lub null, jeśli nie ustawiono
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Ustawia slug tagu.
     *
     * @param string $slug Slug do ustawienia
     */
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
