<?php

/**
 * Tag Service.
 */

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;

/**
 * Serwis do zarządzania tagami (tworzenie, aktualizacja, usuwanie, listowanie).
 */
class TagService
{
    private TagRepository $tagRepository;

    /**
     * Konstruktor serwisu tagów.
     *
     * @param TagRepository $tagRepository Repozytorium tagów
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Tworzy slug i zapisuje nowy tag do bazy danych.
     *
     * @param Tag $tag Nowy tag
     */
    public function createTag(Tag $tag): void
    {
        $slug = $this->generateSlug($tag->getName());
        $tag->setSlug($slug);

        $this->tagRepository->save($tag);
    }

    /**
     * Generuje slug z nazwy tagu (usuwa znaki specjalne, zamienia spacje na myślniki).
     *
     * @param string $name Nazwa tagu
     *
     * @return string Slug
     */
    public function generateSlug(string $name): string
    {
        return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
    }

    /**
     * Aktualizuje istniejący tag (np. po zmianie nazwy).
     *
     * @param Tag $tag Tag do zaktualizowania
     */
    public function updateTag(Tag $tag): void
    {
        $slug = $this->generateSlug($tag->getName());
        $tag->setSlug($slug);

        $this->tagRepository->save($tag);
    }

    /**
     * Usuwa tag.
     *
     * @param Tag $tag Tag do usunięcia
     */
    public function deleteTag(Tag $tag): void
    {
        $this->tagRepository->remove($tag);
    }

    /**
     * Zwraca wszystkie tagi.
     *
     * @return Tag[] Lista tagów
     */
    public function getAllTags(): array
    {
        return $this->tagRepository->findAll();
    }
}
