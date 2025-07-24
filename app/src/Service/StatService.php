<?php

/**
 * Stat Service.
 */

namespace App\Service;

use App\Repository\TagRepository;
use App\Repository\UrlRepository;

/**
 * Serwis do generowania podstawowych statystyk aplikacji.
 */
readonly class StatService
{
    /**
     * @param UrlRepository $urlRepository repozytorium adresów URL
     * @param TagRepository $tagRepository repozytorium tagów
     */
    public function __construct(private UrlRepository $urlRepository, private TagRepository $tagRepository)
    {
    }

    /**
     * Zwraca zestaw podstawowych statystyk.
     *
     * @return array tablica danych statystycznych
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
