<?php

namespace App\Security\Voter;

use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class TicketVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    private const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE], true)
            && $subject instanceof Ticket;
    }

    protected function voteOnAttribute(string $attribute, $ticket, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // Vérification de l'utilisateur connecté
        if (!$user instanceof User) {
            return false;
        }

        // ROLE_ADMIN : accès complet
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // ROLE_TECHNICIEN : VIEW et EDIT uniquement
        if (in_array('ROLE_TECHNICIEN', $user->getRoles(), true)) {
            return in_array($attribute, [self::VIEW, self::EDIT], true);
        }

        // ROLE_USER : VIEW et EDIT sur ses propres tickets
        if (in_array('ROLE_USER', $user->getRoles(), true)) {
            if (in_array($attribute, [self::VIEW, self::EDIT], true)) {
                return $ticket->getOwner() === $user;
            }
            return false;
        }

        return false;
    }
}
