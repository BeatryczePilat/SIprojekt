<?php

/**
 * Admin Tag Controller.
 */

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Service\TagService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Kontroler do zarządzania tagami w panelu administratora.
 */
#[Route('/admin/tag')]
class AdminTagController extends AbstractController
{
    /**
     * Konstruktor kontrolera.
     *
     * @param TranslatorInterface $translator Serwis tłumaczeń
     * @param TagService          $tagService Serwis do operacji na tagach
     */
    public function __construct(private readonly TranslatorInterface $translator, private readonly TagService $tagService)
    {
    }

    /**
     * Wyświetlanie listy wszystkich tagów.
     *
     * @return Response Odpowiedź HTML z listą tagów
     */
    #[Route('/', name: 'admin_tag_index')]
    public function index(): Response
    {
        return $this->render('url/admin_tag.html.twig', [
            'tags' => $this->tagService->getAllTags(),
        ]);
    }

    /**
     * Tworzenie nowego tagu.
     *
     * @param Request $request Obiekt żądania
     *
     * @return Response Odpowiedź HTML z formularzem lub przekierowaniem
     */
    #[Route('/new', name: 'admin_tag_new')]
    public function new(Request $request): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->createTag($tag);
            $this->addFlash('success', $this->translator->trans('flash.tag.created'));

            return $this->redirectToRoute('admin_tag_index');
        }

        return $this->render('url/admin_tag_form.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('form.tag.new_title'),
        ]);
    }

    /**
     * Edycja istniejącego tagu.
     *
     * @param Request $request Obiekt żądania
     * @param Tag     $tag     Encja tagu do edycji
     *
     * @return Response Odpowiedź HTML z formularzem lub przekierowaniem
     */
    #[Route('/{id}/edit', name: 'admin_tag_edit')]
    public function edit(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->updateTag($tag);
            $this->addFlash('success', $this->translator->trans('flash.tag.updated'));

            return $this->redirectToRoute('admin_tag_index');
        }

        return $this->render('url/admin_tag_form.html.twig', [
            'form' => $form->createView(),
            'title' => $this->translator->trans('form.tag.edit_title'),
        ]);
    }

    /**
     * Usuwanie tagu po potwierdzeniu CSRF.
     *
     * @param Request $request Obiekt żądania
     * @param Tag     $tag     Encja tagu do usunięcia
     *
     * @return Response Przekierowanie po usunięciu
     */
    #[Route('/{id}/delete', name: 'admin_tag_delete', methods: ['POST'])]
    public function delete(Request $request, Tag $tag): Response
    {
        if ($this->isCsrfTokenValid('delete-tag-'.$tag->getId(), $request->request->get('_token'))) {
            $this->tagService->deleteTag($tag);
            $this->addFlash('success', $this->translator->trans('flash.tag.deleted'));
        }

        return $this->redirectToRoute('admin_tag_index');
    }
}
