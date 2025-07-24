<?php

/**
 * Admin Controller.
 */

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminProfileType;
use App\Service\AdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Kontroler do zarządzania funkcjami administracyjnymi.
 */
class AdminController extends AbstractController
{
    /**
     * Konstruktor kontrolera.
     *
     * @param TranslatorInterface $translator Serwis tłumaczeń
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Wyświetlanie dashboardu admina.
     *
     * @return Response odpowiedź HTTP z widokiem dashboardu
     */
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin.html.twig');
    }

    /**
     * Edycja profilu zalogowanego administratora.
     *
     * @param Request      $request      Obiekt żądania HTTP
     * @param AdminService $adminService Serwis do zarządzania administratorem
     *
     * @return Response odpowiedź HTTP z formularzem profilu lub przekierowaniem po zapisie
     */
    #[Route('/admin/profil', name: 'admin_profile')]
    #[IsGranted('ROLE_ADMIN')]
    public function profile(Request $request, AdminService $adminService): Response
    {
        /** @var Admin $admin Aktualnie zalogowany administrator */
        $admin = $this->getUser();
        if (!$admin instanceof Admin) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(AdminProfileType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $adminService->updateProfile($admin, $plainPassword);
            $this->addFlash('success', $this->translator->trans('flash.admin.profile_updated'));

            return $this->redirectToRoute('admin_profile');
        }

        return $this->render('admin/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
