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
    /**
     * Konstruktor kontrolera przekierowań URL.
     *
     * @param TranslatorInterface $translator TranslatorInterface $translator
     * @param UrlService          $urlService Serwis do obsługi URL-i
     */
    public function __construct(private readonly TranslatorInterface $translator, private readonly UrlService $urlService)
    {
    }

    /**
     * Przekierowuje użytkownika do oryginalnego adresu URL na podstawie skróconego kodu.
     *
     * @param string $shortCode Skrócony kod adresu
     *
     * @return Response Przekierowanie do oryginalnego adresu/błąd 404
     */
    #[Route('/s/{shortCode}', name: 'url_redirect')]
    public function handleRedirect(string $shortCode): Response
    {
        $url = $this->urlService->handleRedirect($shortCode);

        if (!$url) {
            throw $this->createNotFoundException(
                $this->translator->trans('error.short_code_not_found')
            );
        }

        return $this->redirect($url->getOriginalUrl());
    }
}
