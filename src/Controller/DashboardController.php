<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class DashboardController extends AbstractController
{
    #[Route('/ticket/dashboard', name: 'app_dashboard')]
    public function index(TicketRepository $ticketRepo): Response
    {
        $stats = $ticketRepo->countByStatus();

        // s’assure que tous les statuts sont présents, même à 0
        foreach (Ticket::STATUSES as $st) {
            $stats[$st] = $stats[$st] ?? 0;
        }

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
        ]);
    }
}
