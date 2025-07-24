<?php

/**
 * Url Redirect Controller.
 */

namespace App\Controller;

use App\Service\UrlService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Kontroler odpowiedzialny za obsługę przekierowań na podstawie skróconego kodu.
 */
class UrlRedirectController extends AbstractController
{
    private TranslatorInterface $translator;

    /**
     * Konstruktor kontrolera przekierowań URL.
     *
     * @param TranslatorInterface $translator Tłumacz Symfony
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Przekierowuje użytkownika do oryginalnego adresu URL na podstawie skróconego kodu.
     *
     * @param string     $shortCode  skrócony kod adresu
     * @param UrlService $urlService serwis do obsługi url
     *
     * @return Response przekierowanie do oryginalnego adresu lub błąd 404
     */
    #[Route('/s/{shortCode}', name: 'url_redirect')]
    public function handleRedirect(string $shortCode, UrlService $urlService): Response
    {
        $url = $urlService->handleRedirect($shortCode);

        if (!$url) {
            throw $this->createNotFoundException($this->translator->trans('error.short_code_not_found'));
        }

        return $this->redirect($url->getOriginalUrl());
    }
}
