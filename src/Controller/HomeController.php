<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/users', name: 'app_users')]
    public function Users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('home/users.html.twig',[
            'users' => $users
        ]);
    }
}
