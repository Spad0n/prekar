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

    #[ORM\ManyToOne(inversedBy: 'message_snd')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $id_sender = null;

    #[ORM\ManyToOne(inversedBy: 'message_rcv')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $id_receiver = null;

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

    public function getIdSender(): ?User
    {
        return $this->id_sender;
    }

    public function setIdSender(?User $id_sender): static
    {
        $this->id_sender = $id_sender;

        return $this;
    }

    public function getIdReceiver(): ?User
    {
        return $this->id_receiver;
    }

    public function setIdReceiver(?User $id_receiver): static
    {
        $this->id_receiver = $id_receiver;

        return $this;
    }
}