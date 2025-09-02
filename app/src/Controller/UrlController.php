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
     * Konstruktor kontrolera.
     *
     * @param TranslatorInterface $translator    Serwis
     * @param UrlService          $urlService    Serwis do obsługi adresów
     * @param StatService         $statService   Serwis statystyk
     * @param UrlRepository       $urlRepository Repozytorium URL-i
     */
    public function __construct(private readonly TranslatorInterface $translator, private readonly UrlService $urlService, private readonly StatService $statService, private readonly UrlRepository $urlRepository)
    {
    }

    /**
     * Obsługuje skracanie adresu URL.
     *
     * @param Request $request $request
     *
     * @return Response Odpowiedź z formularzem lub skróconym adresem
     *
     * @throws RandomException
     */
    #[Route('/shorten', name: 'url_shorten')]
    public function shorten(Request $request): Response
    {
        $url = new Url();
        $form = $this->createForm(UrlType::class, $url);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->urlService->createShortUrl($url);

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
     * Wyświetla najnowsze URL-e (z paginacją).
     *
     * @param Request $request $request
     *
     * @return Response Odpowiedź z listą adresów
     */
    #[Route('/latest', name: 'url_latest')]
    public function latest(Request $request): Response
    {
        $pagination = $this->urlService->getLatestUrlsPaginated(
            $request->query->getInt('page', 1)
        );

        return $this->render('url/latest.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Wyświetla adresy URL przypisane do danego tagu.
     *
     * @param string $slug string $slug
     *
     * @return Response Odpowiedź z listą adresów
     */
    #[Route('/tag/{slug}', name: 'url_by_tag')]
    public function byTag(string $slug): Response
    {
        $urls = $this->urlService->getUrlsByTagSlug($slug);

        return $this->render('url/by_tag.html.twig', [
            'urls' => $urls,
            'tag' => $slug,
        ]);
    }

    /**
     * Wyświetla najpopularniejsze URL-e (z paginacją).
     *
     * @param Request $request $request
     *
     * @return Response Odpowiedź z listą adresów
     */
    #[Route('/popular', name: 'url_popular')]
    public function popular(Request $request): Response
    {
        $pagination = $this->urlService->getMostClickedPaginated(
            $request->query->getInt('page', 1)
        );

        return $this->render('url/popular.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Wyświetla statystyki i zestawienia URL-i.
     *
     * @return Response Odpowiedź ze statystykami
     */
    #[Route('/stats', name: 'url_stats')]
    public function stats(): Response
    {
        return $this->render('url/stats.html.twig', [
            'stats' => $this->statService->getStats(),
            'recentUrls' => $this->urlRepository->findBy([], ['createdAt' => 'DESC'], 5),
            'topClickedUrls' => $this->urlRepository->findBy([], ['clicks' => 'DESC'], 5),
            'uniqueEmails' => $this->urlRepository->findUniqueEmails(),
            'tags' => $this->urlRepository->findAllTagsWithCounts(),
        ]);
    }

    /**
     * Obsługuje wyszukiwanie adresów URL.
     *
     * @param Request $request $request
     *
     * @return Response Odpowiedź z wynikami
     */
    #[Route('/search', name: 'url_search')]
    public function search(Request $request): Response
    {
        $form = $this->createForm(UrlFilterType::class);
        $form->handleRequest($request);

        $urls = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $urls = $this->urlService->searchUrls($form->getData());
        }

        return $this->render('url/search.html.twig', [
            'form' => $form->createView(),
            'urls' => $urls,
        ]);
    }
}
