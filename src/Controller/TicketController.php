<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketForm;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

#[Route('/ticket')]
final class TicketController extends AbstractController
{
    #[Route(name: 'app_ticket_index', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository, Request $request): Response
    {
        // Build query selon le rôle
        $qb = $ticketRepository->createQueryBuilder('t');
        if (! $this->isGranted('ROLE_ADMIN') && ! $this->isGranted('ROLE_TECHNICIEN')) {
            $qb->andWhere('t.owner = :user')
                ->setParameter('user', $this->getUser());
        }

        // Pagination Doctrine
        $page  = max(1, $request->query->getInt('page', 1));
        $limit = 10;
        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator  = new Paginator($qb);
        $totalItems = count($paginator);
        $pagesCount = (int) ceil($totalItems / $limit);

        return $this->render('ticket/index.html.twig', [
            'tickets'     => $paginator,
            'currentPage' => $page,
            'pagesCount'  => $pagesCount,
        ]);
    }

    #[Route('/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketForm::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setOwner($this->getUser());
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ticket/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_show', methods: ['GET'])]
    public function show(Ticket $ticket): Response
    {
        $this->denyAccessUnlessGranted('VIEW', $ticket);
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $ticket);
        $form = $this->createForm(TicketForm::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $ticket);

        if ($this->isCsrfTokenValid('delete' . $ticket->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/ticket/{id}/transition/{transition}', name: 'app_ticket_transition')]
    public function transition(
        Ticket $ticket,
        string $transition,
        WorkflowInterface $ticketStateMachine,
        EntityManagerInterface $em
    ): Response {
        // Vérifie que la transition est possible
        if (!$ticketStateMachine->can($ticket, $transition)) {
            throw $this->createAccessDeniedException('Transition invalide.');
        }

        // Applique la transition et met à jour la date
        $ticketStateMachine->apply($ticket, $transition);
        $ticket->setUpdatedAt(new DateTime('today'));
        $em->flush();

        return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
    }
}
