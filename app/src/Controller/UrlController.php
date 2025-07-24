<?php

/**
 * Url Controller.
 */

namespace App\Controller;

use App\Entity\Url;
use App\Form\UrlFilterType;
use App\Form\UrlType;
use App\Repository\UrlRepository;
use App\Service\StatService;
use App\Service\UrlService;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Kontroler do obsługi adresów URL (tworzenie, listowanie, filtrowanie, statystyki).
 */
class UrlController extends AbstractController
{
    /**
     * Konstruktor kontrolera URL.
     *
     * @param TranslatorInterface $translator Tłumacz Symfony
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Obsługa skracania adresu URL.
     *
     * @param Request    $request    Obiekt żądania HTTP
     * @param UrlService $urlService Serwis do obsługi URL
     *
     * @return Response Odpowiedź HTML z formularzem lub skróconym adresem
     *
     * @throws RandomException
     */
    #[Route('/shorten', name: 'url_shorten')]
    public function shorten(Request $request, UrlService $urlService): Response
    {
        $url = new Url();
        $form = $this->createForm(UrlType::class, $url);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $urlService->createShortUrl($url);

            $this->addFlash('success', $this->translator->trans('url.shortened.success'));

            $shortUrl = $this->generateUrl('url_redirect', [
                'shortCode' => $url->getShortCode(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            return $this->render('url/shorten.html.twig', [
                'form' => $form->createView(),
                'shortUrl' => $shortUrl,
            ]);
        }

        return $this->render('url/shorten.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Wyświetla najnowsze adresy URL (paginacja).
     *
     * @param int        $page       Numer strony
     * @param UrlService $urlService Serwis do obsługi URL
     *
     * @return Response Odpowiedź HTML z listą adresów
     */
    #[Route('/latest/{page}', name: 'url_latest', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function latest(int $page, UrlService $urlService): Response
    {
        $result = $urlService->getLatestUrlsPaginated($page);

        return $this->render('url/latest.html.twig', [
            'urls' => $result['data'],
            'pages' => $result['pages'],
            'currentPage' => $page,
        ]);
    }

    /**
     * Wyświetla adresy URL powiązane z danym tagiem.
     *
     * @param string     $slug       Identyfikator tagu
     * @param UrlService $urlService Serwis do obsługi URL
     *
     * @return Response Odpowiedź HTML z listą adresów
     */
    #[Route('/tag/{slug}', name: 'url_by_tag')]
    public function byTag(string $slug, UrlService $urlService): Response
    {
        $urls = $urlService->getUrlsByTagSlug($slug);

        return $this->render('url/by_tag.html.twig', [
            'urls' => $urls,
            'tag' => $slug,
        ]);
    }

    /**
     * Wyświetla najpopularniejsze adresy URL (paginacja).
     *
     * @param int        $page       Numer strony
     * @param UrlService $urlService Serwis do obsługi URL
     *
     * @return Response Odpowiedź HTML z listą adresów
     */
    #[Route('/popular/{page}', name: 'url_popular', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function popular(int $page, UrlService $urlService): Response
    {
        $result = $urlService->getMostClickedPaginated($page);

        return $this->render('url/popular.html.twig', [
            'urls' => $result['data'],
            'pages' => $result['pages'],
            'currentPage' => $page,
        ]);
    }

    /**
     * Wyświetla statystyki i zestawienia URL.
     *
     * @param StatService   $statService   Serwis do generowania statystyk
     * @param UrlRepository $urlRepository Repozytorium adresów URL
     *
     * @return Response Odpowiedź HTML ze statystykami
     */
    #[Route('/stats', name: 'url_stats')]
    public function stats(StatService $statService, UrlRepository $urlRepository): Response
    {
        return $this->render('url/stats.html.twig', [
            'stats' => $statService->getStats(),
            'recentUrls' => $urlRepository->findBy([], ['createdAt' => 'DESC'], 5),
            'topClickedUrls' => $urlRepository->findBy([], ['clicks' => 'DESC'], 5),
            'uniqueEmails' => $urlRepository->findUniqueEmails(),
            'tags' => $urlRepository->findAllTagsWithCounts(),
        ]);
    }

    /**
     * Obsługa wyszukiwania adresów URL.
     *
     * @param Request    $request    Obiekt żądania HTTP
     * @param UrlService $urlService Serwis do obsługi URL
     *
     * @return Response Odpowiedź HTML z wynikami wyszukiwania
     */
    #[Route('/search', name: 'url_search')]
    public function search(Request $request, UrlService $urlService): Response
    {
        $form = $this->createForm(UrlFilterType::class);
        $form->handleRequest($request);

        $urls = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $urls = $urlService->searchUrls($form->getData());
        }

        return $this->render('url/search.html.twig', [
            'form' => $form->createView(),
            'urls' => $urls,
        ]);
    }
}
