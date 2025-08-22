<?php

/**
 * Security Controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController.
 */
class SecurityController extends AbstractController
{
    /**
     * Konstruktor kontrolera.
     *
     * @param AuthenticationUtils $authUtils $authUtils
     */
    public function __construct(private readonly AuthenticationUtils $authUtils)
    {
    }

    /**
     * Wyświetla formularz logowania oraz ewentualne błędy logowania.
     *
     * @return Response Response
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $this->authUtils->getLastUsername(),
            'error' => $this->authUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * Punkt wejścia do wylogowania użytkownika.
     * Ta metoda jest przechwytywana przez mechanizm firewall Symfony.
     *
     * @throws \LogicException
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method is intercepted by the logout key in your firewall configuration.');
    }
}
