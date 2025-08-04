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

        $form = $this->createForm(AdminProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $current = $form->get('currentPassword')->getData();
            $new = $form->get('newPassword')->getData();

            if ($new) {
                $success = $this->adminService->changePasswordWithVerification($user, $current, $new);

                if (!$success) {
                    $this->addFlash('danger', $this->translator->trans('flash.password.invalid'));

                    return $this->render('url/admin_change_password.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
            } else {
                // Zapis tylko adresu e-mail
                $this->adminService->saveProfile($user);
            }

            $this->addFlash('success', $this->translator->trans('flash.credentials.updated'));

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('url/admin_change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
