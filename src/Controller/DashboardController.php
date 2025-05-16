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
        // Récupère les stats dynamiques
        $statusStats   = $ticketRepo->countByStatus();
        $priorityStats = $ticketRepo->countByPriority();

        // Garantie clés même à zéro
        foreach (Ticket::STATUSES   as $st) { $statusStats[$st]   = $statusStats[$st]   ?? 0; }
        foreach (Ticket::PRIORITIES as $pr) { $priorityStats[$pr] = $priorityStats[$pr] ?? 0; }

        return $this->render('dashboard/index.html.twig', [
            'stats'      => $statusStats,
            'priorities' => $priorityStats,
        ]);
    }
}
