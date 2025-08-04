<?php

/**
 * Tag Service.
 */

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;

/**
 * Serwis operujący na encjach Tag.
 */
readonly class TagService
{
    /**
     * Konstruktor z wstrzyknięciem zależności.
     *
     * @param TagRepository $tagRepository Repozytorium obsługujące encję Tag
     */
    public function __construct(private TagRepository $tagRepository)
    {
    }

    /**
     * Tworzy nowy tag – generuje slug i zapisuje go w bazie danych.
     *
     * @param Tag $tag Nowy tag do zapisania
     */
    public function createTag(Tag $tag): void
    {
        $tag->setSlug($this->generateSlug($tag->getName()));
        $this->tagRepository->save($tag);
    }

    /**
     * Aktualizuje istniejący tag – w tym odświeża jego slug.
     *
     * @param Tag $tag Tag do zaktualizowania
     */
    public function updateTag(Tag $tag): void
    {
        $tag->setSlug($this->generateSlug($tag->getName()));
        $this->tagRepository->save($tag);
    }

    /**
     * Usuwa tag z bazy danych.
     *
     * @param Tag $tag Tag do usunięcia
     */
    public function deleteTag(Tag $tag): void
    {
        $this->tagRepository->remove($tag);
    }

    /**
     * Zwraca wszystkie dostępne tagi z bazy.
     *
     * @return Tag[] Tablica obiektów tagów
     */
    public function getAllTags(): array
    {
        return $this->tagRepository->findAll();
    }

    /**
     * Generuje slug z nazwy tagu.
     * Zastępuje niedozwolone znaki myślnikami, usuwa spacje i konwertuje na małe litery.
     *
     * @param string $name Nazwa tagu
     *
     * @return string string
     */
    private function generateSlug(string $name): string
    {
        return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
    }
}
