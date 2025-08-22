<?php

/**
 * Stat Service.
 */

namespace App\Service;

use App\Repository\TagRepository;
use App\Repository\UrlRepository;

/**
 * Klasa serwisowa do generowania statystyk aplikacji.
 */
readonly class StatService
{
    /**
     * Konstruktor z wstrzyknięciem zależności.
     *
     * @param UrlRepository $urlRepository Repozytorium adresów URL
     * @param TagRepository $tagRepository Repozytorium tagów
     */
    public function __construct(private UrlRepository $urlRepository, private TagRepository $tagRepository)
    {
    }

    /**
     * Zwraca podstawowe statystyki aplikacji.
     *
     * @return array<string, int> Tablica ze statystykami
     */
    public function getStats(): array
    {
        return [
            'totalUrls' => $this->urlRepository->countAll(),
            'totalClicks' => $this->urlRepository->sumClicks(),
            'uniqueEmails' => $this->urlRepository->countUniqueEmails(),
            'totalTags' => $this->tagRepository->countAll(),
        ];
    }
}
