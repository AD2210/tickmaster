<?php

namespace App\Controller;

use App\Entity\Attachment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AttachementController extends AbstractController
{
    #[Route('/attachment/{id}/delete', name: 'attachment_delete', methods: ['POST'])]
    public function delete(
        Attachment $attachment,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isCsrfTokenValid('delete-attachment' . $attachment->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('CSRF token invalide.');
        }

        // Suppression du fichier physique
        $filesystem = new Filesystem();
        $filesystem->remove($this->getParameter('attachments_directory') . '/' . $attachment->getFilename());

        // Suppression de l'entité
        $em->remove($attachment);
        $em->flush();

        // Redirection vers le ticket parent
        return $this->redirectToRoute('app_ticket_show', [
            'id' => $attachment->getComment()->getTicket()->getId(),
        ]);
    }
}
