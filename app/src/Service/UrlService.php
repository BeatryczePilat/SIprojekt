<?php

/**
 * Url Service.
 */

namespace App\Service;

use App\Entity\Url;
use App\Repository\UrlRepository;
use DateTimeImmutable;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Random\RandomException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Serwis do zarządzania adresami URL (tworzenie, edycja, wyszukiwanie, przekierowania).
 */
readonly class UrlService
{
    /**
     * Konstruktor serwisu URL.
     *
     * @param UrlRepository $urlRepository
     * @param PaginatorInterface $paginator
     * @param RequestStack $requestStack
     */
    public function __construct(
        private UrlRepository      $urlRepository,
        private PaginatorInterface $paginator,
        private RequestStack       $requestStack,
    ) {}


    /**
     * Tworzy nowy skrócony URL.
     *
     * @param Url $url Encja z formularza
     *
     * @throws RandomException
     */
    public function createShortUrl(Url $url): void
    {
        $shortCode = substr(bin2hex(random_bytes(4)), 0, 6);
        $url->setShortCode($shortCode);
        $url->setClicks(0);

        if (!$url->getCreatedAt()) {
            $url->setCreatedAt(new DateTimeImmutable());
        }

        $url->setUpdatedAt(new DateTimeImmutable());
        $this->urlRepository->save($url);
    }

    /**
     * Pobiera najnowsze URL-e z paginacją.
     *
     * @param int $page Numer strony
     *
     * @return PaginationInterface Obiekt paginacji
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
     * Zwraca URL-e przypisane do tagu.
     *
     * @param string $slug Slug tagu
     *
     * @return Url[] Lista URL-i
     */
    public function getUrlsByTagSlug(string $slug): array
    {
        return $this->urlRepository->findByTagSlug($slug);
    }

    /**
     * Zwraca najczęściej klikane linki z paginacją.
     *
     * @param int $page Numer strony
     *
     * @return PaginationInterface Obiekt paginacji
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
     * @param Url $url Encja po edycji
     */
    public function updateUrl(Url $url): void
    {
        $url->setUpdatedAt(new DateTimeImmutable());
        $this->urlRepository->save($url);
    }

    /**
     * Usuwa URL.
     *
     * @param Url $url Encja do usunięcia
     */
    public function deleteUrl(Url $url): void
    {
        $this->urlRepository->remove($url);
    }

    /**
     * Zwraca wszystkie URL-e posortowane malejąco po dacie.
     *
     * @return Url[] Lista URL-i
     */
    public function getAllSorted(): array
    {
        return $this->urlRepository->findBy([], ['createdAt' => 'DESC']);
    }

    /**
     * Zwraca URL-e dopasowane do filtrów wyszukiwania.
     *
     * @param array<string, mixed> $filters Dane z formularza
     *
     * @return Url[] Wyniki
     */
    public function searchUrls(array $filters): array
    {
        return $this->urlRepository->findByFilters($filters);
    }

    /**
     * Obsługuje przekierowanie oraz zlicza kliknięcia.
     *
     * @param string $shortCode Skrócony kod
     *
     * @return Url|null URL jeśli znaleziony, null w przeciwnym razie
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
