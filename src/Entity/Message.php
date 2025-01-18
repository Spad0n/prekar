<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'message_sent')]
    #[ORM\JoinTable(name: 'message_sent')]
    private Collection $message_snd;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'message_rcved')]
    #[ORM\JoinTable(name: 'message_received')]
    private Collection $message_rcv;

    public function __construct()
    {
        $this->message_snd = new ArrayCollection();
        $this->message_rcv = new ArrayCollection();
    }


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

    /**
     * @return Collection<int, User>
     */
    public function getMessageSnd(): Collection
    {
        return $this->message_snd;
    }

    public function addMessageSnd(User $messageSnd): static
    {
        if (!$this->message_snd->contains($messageSnd)) {
            $this->message_snd->add($messageSnd);
        }

        return $this;
    }

    public function removeMessageSnd(User $messageSnd): static
    {
        $this->message_snd->removeElement($messageSnd);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMessageRcv(): Collection
    {
        return $this->message_rcv;
    }

    public function addMessageRcv(User $messageRcv): static
    {
        if (!$this->message_rcv->contains($messageRcv)) {
            $this->message_rcv->add($messageRcv);
        }

        return $this;
    }

    public function removeMessageRcv(User $messageRcv): static
    {
        $this->message_rcv->removeElement($messageRcv);

        return $this;
    }


}
