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
 * Kontroler odpowiedzialny za logowanie i wylogowywanie użytkowników.
 */
class SecurityController extends AbstractController
{
    /**
     * Wyświetla formularz logowania i przetwarza ewentualne błędy logowania.
     *
     * @param AuthenticationUtils $authenticationUtils Narzędzie do obsługi procesu logowania
     *
     * @return Response Odpowiedź HTML z formularzem logowania
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Pobiera ewentualny błąd logowania
        $error = $authenticationUtils->getLastAuthenticationError();

        // Pobiera ostatnio wpisaną nazwę użytkownika
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
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
