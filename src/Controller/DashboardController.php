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
        /** @var User $user */
        $user = $this->getUser();

        if ($this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_TECHNICIEN') && !$this->isGranted('ROLE_ADMIN')) {
            // USER: stats only for own tickets
            $qb1 = $ticketRepo->createQueryBuilder('t')
                ->select('t.status AS status, COUNT(t.id) AS cnt')
                ->andWhere('t.owner = :user')
                ->setParameter('user', $user)
                ->groupBy('t.status');
            $stats = array_column($qb1->getQuery()->getResult(), 'cnt', 'status');

            $qb2 = $ticketRepo->createQueryBuilder('t')
                ->select('t.priority AS priority, COUNT(t.id) AS cnt')
                ->andWhere('t.owner = :user')
                ->setParameter('user', $user)
                ->groupBy('t.priority');
            $priorities = array_column($qb2->getQuery()->getResult(), 'cnt', 'priority');
        } else {
            // TECHNICIEN & ADMIN: all tickets
            $stats = $ticketRepo->countByStatus();
            $priorities = $ticketRepo->countByPriority();
        }

        // ensure all keys present
        foreach (Ticket::STATUSES   as $st) {
            $stats[$st] = $stats[$st] ?? 0;
        }
        foreach (Ticket::PRIORITIES as $pr) {
            $priorities[$pr] = $priorities[$pr] ?? 0;
        }

        return $this->render('dashboard/index.html.twig', [
            'stats'      => $stats,
            'priorities' => $priorities,
        ]);
    }

}
