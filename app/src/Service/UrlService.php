<?php

/**
 * Serwis UrlService.
 */

namespace App\Service;

use App\Entity\Url;
use App\Repository\UrlRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Random\RandomException;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Serwis do zarządzania skracanymi URL-ami.
 */
readonly class UrlService
{
    /**
     * Konstruktor – wstrzykiwanie zależności (DI).
     *
     * @param UrlRepository      $urlRepository Repozytorium encji URL
     * @param PaginatorInterface $paginator     Komponent paginacji KNP
     * @param RequestStack       $requestStack  Stos żądań HTTP
     */
    public function __construct(private UrlRepository $urlRepository, private PaginatorInterface $paginator, private RequestStack $requestStack)
    {
    }

    /**
     * Tworzy nowy skrócony adres URL.
     *
     * @param Url $url Nowa encja URL
     *
     * @throws RandomException Gdy nie uda się wygenerować kodu
     */
    public function createShortUrl(Url $url): void
    {
        $shortCode = substr(bin2hex(random_bytes(4)), 0, 6);
        $url->setShortCode($shortCode);
        $url->setClicks(0);

        if (!$url->getCreatedAt()) {
            $url->setCreatedAt(new \DateTimeImmutable());
        }

        $url->setUpdatedAt(new \DateTimeImmutable());

        $this->urlRepository->save($url);
    }

    /**
     * Zwraca najnowsze URL-e z paginacją.
     *
     * @param int $page Numer strony
     *
     * @return PaginationInterface Wyniki z paginacją
     */
    public function getLatestUrlsPaginated(int $page): PaginationInterface
    {
        $query = $this->urlRepository->findLatestQuery();
        $request = $this->requestStack->getCurrentRequest();

        return $this->paginator->paginate(
            $query,
            $request->query->getInt('page', $page),
            UrlRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Zwraca URL-e przypisane do podanego sluga tagu.
     *
     * @param string $slug Slug tagu
     *
     * @return Url[] Lista dopasowanych URL-i
     */
    public function getUrlsByTagSlug(string $slug): array
    {
        return $this->urlRepository->findByTagSlug($slug);
    }

    /**
     * Zwraca najczęściej klikane URL-e z paginacją.
     *
     * @param int $page Numer strony
     *
     * @return PaginationInterface Wyniki z paginacją
     */
    public function getMostClickedPaginated(int $page): PaginationInterface
    {
        $query = $this->urlRepository->findMostClickedQuery();
        $request = $this->requestStack->getCurrentRequest();

        return $this->paginator->paginate(
            $query,
            $request->query->getInt('page', $page),
            UrlRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Aktualizuje istniejący URL.
     *
     * @param Url $url Encja do zapisania
     */
    public function updateUrl(Url $url): void
    {
        $url->setUpdatedAt(new \DateTimeImmutable());
        $this->urlRepository->save($url);
    }

    /**
     * Usuwa podany URL.
     *
     * @param Url $url Encja do usunięcia
     */
    public function deleteUrl(Url $url): void
    {
        $this->urlRepository->remove($url);
    }

    /**
     * Zwraca wszystkie URL-e, posortowane malejąco po dacie utworzenia.
     *
     * @return Url[] Lista URL-i
     */
    public function getAllSorted(): array
    {
        return $this->urlRepository->findBy([], ['createdAt' => 'DESC']);
    }

    /**
     * Przeszukuje URL-e według podanych filtrów.
     *
     * @param array<string, mixed> $filters Dane z formularza wyszukiwania
     *
     * @return Url[] Lista pasujących adresów URL
     */
    public function searchUrls(array $filters): array
    {
        return $this->urlRepository->findByFilters($filters);
    }

    /**
     * Obsługuje przekierowanie i inkrementuje licznik kliknięć.
     *
     * @param string $shortCode Skrócony kod URL
     *
     * @return Url|null Url|null
     */
    public function handleRedirect(string $shortCode): ?Url
    {
        $url = $this->urlRepository->findOneBy(['shortCode' => $shortCode]);

        if (!$url) {
            return null;
        }

        $url->setClicks($url->getClicks() + 1);
        $this->urlRepository->save($url);

        return $url;
    }
}
