<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SettingsForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_user_settings')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(SettingsForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les valeurs du formulaire
            $settings = [
                'theme'      => $form->get('theme')->getData(),
                'showCharts' => $form->get('showCharts')->getData(),
                'barColor'      => $form->get('barColor')->getData(),
                'pieContrast' => $form->get('pieContrast')->getData(),
            ];
            $user->setSettings($settings);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Paramètres enregistrés.');
            return $this->redirectToRoute('app_user_settings');
        }

        return $this->render('settings/index.html.twig', [
            'form' => $form,
        ]);
    }
}
