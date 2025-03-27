<?php

namespace App\Entity;

use App\Repository\RentingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentingRepository::class)]
class Renting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $nbKm = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $commentary = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;




    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Offer $offer = null;



    #[ORM\ManyToOne(inversedBy: 'rentings')]
    private ?User $userBorrower = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $deliveryLocation = null;

    #[ORM\Column(nullable: true)]
    private ?float $totalAmount = null;

    #[ORM\Column(nullable: true)]
    private ?bool $done = null;

    #[ORM\Column(nullable: true)]
    private ?bool $proceeded = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbKm(): ?string
    {
        return $this->nbKm;
    }

    public function setNbKm(?string $nbKm): static
    {
        $this->nbKm = $nbKm;

        return $this;
    }

    public function getCommentary(): ?string
    {
        return $this->commentary;
    }

    public function setCommentary(string $commentary): static
    {
        $this->commentary = $commentary;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }



    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    public function setOffer(?Offer $offer): static
    {
        $this->offer = $offer;

        return $this;
    }


    public function getUserBorrower(): ?User
    {
        return $this->userBorrower;
    }

    public function setUserBorrower(?User $userBorrower): static
    {
        $this->userBorrower = $userBorrower;

        return $this;
    }

    public function getDeliveryLocation(): ?string
    {
        return $this->deliveryLocation;
    }

    public function setDeliveryLocation(?string $deliveryLocation): static
    {
        $this->deliveryLocation = $deliveryLocation;

        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(?float $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }



    public function isDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(bool $done): static
    {
        $this->done = $done;

        return $this;
    }

    public function isProceeded(): ?bool
    {
        return $this->proceeded;
    }

    public function setProceeded(?bool $proceeded): static
    {
        $this->proceeded = $proceeded;

        return $this;
    }

}
