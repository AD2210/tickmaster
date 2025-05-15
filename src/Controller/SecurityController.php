<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupère la dernière erreur de connexion (null si tout va bien)
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère le dernier nom d’utilisateur (email) saisi pour le pré-remplir
        $lastUsername = $authenticationUtils->getLastUsername();

        // On passe ces deux variables au template pour affichage
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \Exception('Cette méthode est interceptée par la configuration du firewall logout.');
    }
}
