<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TicketHistoryRepository;

#[ORM\Entity(repositoryClass: TicketHistoryRepository::class)]
class TicketHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ticketHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ticket $ticket = null;

    #[ORM\ManyToOne(inversedBy: 'ticketHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $field = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $oldValue = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $newValue = null;

    #[ORM\Column]
    private ?DateTimeImmutable $changedAt = null;

    public function __construct()
    {
        $this->changedAt = new DateTimeImmutable('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): static
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function setField(?string $field): static
    {
        $this->field = $field;

        return $this;
    }

    public function getOldValue(): ?string
    {
        return $this->oldValue;
    }

    public function setOldValue(?string $oldValue): static
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    public function getNewValue(): ?string
    {
        return $this->newValue;
    }

    public function setNewValue(?string $newValue): static
    {
        $this->newValue = $newValue;

        return $this;
    }

    public function getChangedAt(): ?DateTimeImmutable
    {
        return $this->changedAt;
    }

    public function setChangedAt(DateTimeImmutable $changedAt): static
    {
        $this->changedAt = $changedAt;

        return $this;
    }
}
