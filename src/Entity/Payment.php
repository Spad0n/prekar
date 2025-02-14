<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $payDate = null;

    #[ORM\OneToOne(inversedBy: 'payment', cascade: ['persist', 'remove'])]
    private ?Service $apply = null;


    #[ORM\ManyToOne(inversedBy: 'payments')]
    private ?Admin $admin = null;


    #[ORM\ManyToOne(inversedBy: 'payments')]
    private ?User $userOwner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPayDate(): ?\DateTimeInterface
    {
        return $this->payDate;
    }

    public function setPayDate(\DateTimeInterface $payDate): static
    {
        $this->payDate = $payDate;

        return $this;
    }

    public function getApply(): ?Service
    {
        return $this->apply;
    }

    public function setApply(?Service $apply): static
    {
        $this->apply = $apply;

        return $this;
    }


    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(?Admin $admin): static
    {
        $this->admin = $admin;

        return $this;
    }


    public function getUserOwner(): ?User
    {
        return $this->userOwner;
    }

    public function setUserOwner(?User $userOwner): static
    {
        $this->userOwner = $userOwner;

        return $this;
    }
}
