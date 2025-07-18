<?php

namespace App\Controller;

use DateTime;
use App\Entity\Ticket;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Form\TicketForm;
use App\Form\CommentForm;
use App\Entity\Attachment;
use App\Entity\TicketHistory;
use App\Form\TicketFilterForm;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/ticket')]
final class TicketController extends AbstractController
{
    #[Route(name: 'app_ticket_index', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository, Request $request): Response
    {
        // Création du formulaire de filtre
        $form = $this->createForm(TicketFilterForm::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
        $form->handleRequest($request);

        // Requête builder
        $qb = $ticketRepository->createQueryBuilder('t');
        // Filtrage statut et priorité
        if ($form->get('status')->getData()) {
            $qb->andWhere('t.status = :status')
                ->setParameter('status', $form->get('status')->getData());
        }
        if ($form->get('priority')->getData()) {
            $qb->andWhere('t.priority = :priority')
                ->setParameter('priority', $form->get('priority')->getData());
        }
        //Filtrage dates création
        if ($form->get('createdFrom')->getData()) {
            $qb->andWhere('t.createdAt >= :from')
                ->setParameter('from', $form->get('createdFrom')->getData());
        }
        if ($form->get('createdTo')->getData()) {
            $qb->andWhere('t.createdAt <= :to')
                ->setParameter('to', $form->get('createdTo')->getData());
        }
        // Dates update
        if ($form->get('updatedFrom')->getData()) {
            $qb->andWhere('t.updatedAt >= :uFrom')
                ->setParameter('uFrom', $form->get('updatedFrom')->getData());
        }
        if ($form->get('updatedTo')->getData()) {
            $qb->andWhere('t.updatedAt <= :uTo')
                ->setParameter('uTo', $form->get('updatedTo')->getData());
        }
        // Tri
        if ($form->get('sort')->getData()) {
            $direction = $form->get('direction')->getData() ?: 'ASC';
            $qb->orderBy($form->get('sort')->getData(), $direction);
        }

        // Build query selon le rôle
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
            'filterForm' => $form,
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

    #[Route('/{id}', name: 'app_ticket_show', methods: ['GET', 'POST'])]
    public function show(Ticket $ticket, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('VIEW', $ticket);

        // Formulaire commentaire
        $comment = new Comment();
        $comment->setTicket($ticket)->setUser($this->getUser());
        $form = $this->createForm(CommentForm::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form->get('attachments')->getData();
            foreach ($files as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('attachments_directory'), $safeName);

                $attachment = new Attachment();
                $attachment->setOriginalName($originalName)
                    ->setFilename($safeName)
                    ->setComment($comment);
                $em->persist($attachment);
                $comment->addAttachment($attachment);
            }
            // Enregistrer l'historique du commentaire
            $history = new TicketHistory();
            $history->setTicket($ticket)
                ->setUser($this->getUser())
                ->setField('comment')
                ->setOldValue(null)
                ->setNewValue(substr($comment->getContent(), 0, 100));
            $em->persist($history);

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
        }

        // Récupérer les commentaires
        $comments = $ticket->getComments();

        $history = $ticket->getTicketHistories();

        return $this->render('ticket/show.html.twig', [
            'ticket'      => $ticket,
            'comments'    => $comments,
            'commentForm' => $form->createView(),
            'history' => $history,
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

        // Applique la transition et met à jour la date, ainsi que l'historique
        $oldStatus = $ticket->getStatus();
        $ticketStateMachine->apply($ticket, $transition);
        $ticket->setUpdatedAt(new DateTime('today'));

        // Historique des changements de statut
        $history = new TicketHistory();
        $history->setTicket($ticket)
            ->setUser($this->getUser())
            ->setField('status')
            ->setOldValue($oldStatus)
            ->setNewValue($ticket->getStatus());
        $em->persist($history);

        $em->flush();

        return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
    }
}
