<?php

/**
 * Admin Url Controller.
 */

namespace App\Controller;

use App\Entity\Url;
use App\Form\UrlType;
use App\Service\UrlService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Kontroler do zarządzania adresami URL w panelu administratora.
 */
#[Route('/admin/url')]
class AdminUrlController extends AbstractController
{
    /**
     * @param TranslatorInterface $translator Serwis tłumaczeń
     */
    public function __construct(private TranslatorInterface $translator)
    {
    }

    /**
     * Wyświetla listę wszystkich skróconych adresów URL.
     *
     * @param UrlService $urlService Serwis do obsługi adresów URL
     *
     * @return Response Widok z listą adresów
     */
    #[Route('/', name: 'admin_url_index')]
    public function index(UrlService $urlService): Response
    {
        return $this->render('url/admin_url.html.twig', [
            'urls' => $urlService->getAllSorted(),
        ]);
    }

    /**
     * Edytuje istniejący skrócony adres URL.
     *
     * @param Request    $request    Obiekt żądania HTTP
     * @param Url        $url        Encja adresu do edycji
     * @param UrlService $urlService Serwis do obsługi adresów URL
     *
     * @return Response Widok formularza edycji lub przekierowanie
     */
    #[Route('/{id}/edit', name: 'admin_url_edit')]
    public function edit(Request $request, Url $url, UrlService $urlService): Response
    {
        $form = $this->createForm(UrlType::class, $url);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $urlService->updateUrl($url);
            $this->addFlash('success', $this->translator->trans('flash.url.updated'));

            return $this->redirectToRoute('admin_url_index');
        }

        return $this->render('url/admin_url_form.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('form.url.edit_title'),
        ]);
    }

    /**
     * Usuwa adres URL po weryfikacji tokena CSRF.
     *
     * @param Request    $request    Obiekt żądania HTTP
     * @param Url        $url        Encja adresu do usunięcia
     * @param UrlService $urlService Serwis do obsługi adresów URL
     *
     * @return Response Przekierowanie po usunięciu
     */
    #[Route('/{id}/delete', name: 'admin_url_delete', methods: ['POST'])]
    public function delete(Request $request, Url $url, UrlService $urlService): Response
    {
        if ($this->isCsrfTokenValid('delete-url-'.$url->getId(), $request->request->get('_token'))) {
            $urlService->deleteUrl($url);
            $this->addFlash('success', $this->translator->trans('flash.url.deleted'));
        }

        return $this->redirectToRoute('admin_url_index');
    }
}
