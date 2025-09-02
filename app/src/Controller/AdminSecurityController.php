<?php

/**
 * Admin Security Controller.
 */

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminProfileDataType;
use App\Form\AdminPasswordType;
use App\Service\AdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Kontroler odpowiedzialny za bezpieczeństwo i dane logowania administratora.
 */
#[Route('/admin')]
class AdminSecurityController extends AbstractController
{
    /**
     * Konstruktor kontrolera.
     *
     * @param TranslatorInterface $translator   Serwis
     *                                          tłumaczeń
     * @param AdminService        $adminService Serwis zarządzający
     *                                          adminem
     */
    public function __construct(private readonly TranslatorInterface $translator, private readonly AdminService $adminService)
    {
    }

    /**
     * Edycja danych administratora (e-mail i opcjonalnie hasło).
     *
     * @param Request $request Request $request
     *
     * @return Response Odpowiedź HTML z formularzem lub przekierowaniem
     */
    #[Route('/change-password', name: 'admin_change_password')]
    public function changePassword(Request $request): Response
    {
        /** @var Admin|null $user */
        $user = $this->getUser();

        if (!$user instanceof Admin) {
            throw $this->createAccessDeniedException();
        }

        // Formularz do zmiany danych profilu (e-mail, nickname itp.)
        $dataForm = $this->createForm(AdminProfileDataType::class, $user);
        $dataForm->handleRequest($request);

        if ($dataForm->isSubmitted() && $dataForm->isValid()) {
            $this->adminService->saveProfile($user);
            $this->addFlash('success', $this->translator->trans('flash.admin.profile_updated'));

            return $this->redirectToRoute('admin_dashboard');
        }

        // Formularz do zmiany hasła
        $passwordForm = $this->createForm(AdminPasswordType::class);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $current = $passwordForm->get('currentPassword')->getData();
            $new = $passwordForm->get('newPassword')->getData();

            $success = $this->adminService->changePasswordWithVerification($user, $current, $new);

            if (!$success) {
                $this->addFlash('danger', $this->translator->trans('flash.password.invalid'));
            } else {
                $this->addFlash('success', $this->translator->trans('flash.credentials.updated'));
            }

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('url/admin_change_password.html.twig', [
            'dataForm' => $dataForm->createView(),
            'passwordForm' => $passwordForm->createView(),
        ]);
    }
}
