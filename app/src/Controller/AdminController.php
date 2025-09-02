<?php

/**
 * Admin Controller.
 */

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminPasswordType;
use App\Form\AdminProfileDataType;
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
     * @param TranslatorInterface $translator   Serwis tłumaczeń
     * @param AdminService        $adminService Serwis zarządzający adminem
     */
    public function __construct(private readonly TranslatorInterface $translator, private readonly AdminService $adminService)
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

        $admin = $this->getUser();
        if (!$admin instanceof Admin) {
            throw $this->createAccessDeniedException();
        }

        $dataForm = $this->createForm(AdminProfileDataType::class, $admin, [
            'validation_groups' => ['profile'],
        ]);
        $dataForm->handleRequest($request);

        if ($dataForm->isSubmitted() && $dataForm->isValid()) {
            $adminService->updateProfile($admin);
            $this->addFlash('success', $this->translator->trans('flash.admin.profile_updated'));

            return $this->redirectToRoute('admin_profile');
        }

        // Formularz zmiany hasła
        $passwordForm = $this->createForm(AdminPasswordType::class, null, [
            'validation_groups' => ['password'],
        ]);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $currentPassword = $passwordForm->get('currentPassword')->getData();
            $newPassword     = $passwordForm->get('newPassword')->getData();

            $success = $adminService->updateProfileWithPasswordVerification(
                $admin,
                $currentPassword,
                $newPassword
            );

            if ($success) {
                $this->addFlash('success', $this->translator->trans('flash.credentials.updated'));
            } else {
                $this->addFlash('error', $this->translator->trans('admin.password.incorrect'));
            }

            return $this->redirectToRoute('admin_profile');
        }

        return $this->render('admin/change_password.html.twig', [
            'dataForm'     => $dataForm->createView(),
            'passwordForm' => $passwordForm->createView(),
        ]);
    }
}
