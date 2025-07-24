<?php

/**
 * Admin Security Controller.
 */

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminProfileType;
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
     * @param TranslatorInterface $translator Serwis tłumaczeń
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Edycja danych administratora (e-mail i opcjonalnie hasło).
     *
     * @param Request      $request      Obiekt żądania HTTP
     * @param AdminService $adminService Serwis do obsługi administratora
     *
     * @return Response Odpowiedź HTML z formularzem lub przekierowaniem
     */
    #[Route('/change-password', name: 'admin_change_password')]
    public function changePassword(Request $request, AdminService $adminService): Response
    {
        /** @var Admin|null $user */
        $user = $this->getUser();

        if (!$user instanceof Admin) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(AdminProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $current = $form->get('currentPassword')->getData();
            $new = $form->get('newPassword')->getData();

            if ($new) {
                $success = $adminService->changePasswordWithVerification($user, $current, $new);

                if (!$success) {
                    $this->addFlash('danger', $this->translator->trans('flash.password.invalid'));

                    return $this->render('url/admin_change_password.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
            } else {
                // Zapis e-maila nawet jeśli hasło nie zostało zmienione
                $adminService->saveProfile($user);
            }

            $this->addFlash('success', $this->translator->trans('flash.credentials.updated'));

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('url/admin_change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
