<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: "App\Repository\SubscriptionRepository")]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank]
    private $startDate;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\Choice(choices: ["annual", "monthly", "weekly"])]
    #[Assert\NotBlank]
    private $type;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "subscriptions")]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    // Getters and setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
