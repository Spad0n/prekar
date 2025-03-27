<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $text = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateMessage = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $timeMessage = null;

    #[ORM\ManyToOne(inversedBy: 'message_snd')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[ORM\ManyToOne(inversedBy: 'message_rcv')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $receiver = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getDateMessage(): ?\DateTimeInterface
    {
        return $this->dateMessage;
    }

    public function setDateMessage(\DateTimeInterface $dateMessage): static
    {
        $this->dateMessage = $dateMessage;
        return $this;
    }

    public function getTimeMessage(): ?\DateTimeInterface
    {
        return $this->timeMessage;
    }

    public function setTimeMessage(\DateTimeInterface $timeMessage): static
    {
        $this->timeMessage = $timeMessage;
        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }
    
    public function setSender(?User $sender): static
    {
        $this->sender = $sender;
        return $this;
    }
    
    public function getReceiver(): ?User
    {
        return $this->receiver;
    }
    
    public function setReceiver(?User $receiver): static
    {
        $this->receiver = $receiver;
        return $this;
    }
    
}