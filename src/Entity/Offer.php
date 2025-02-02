<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Car $car = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Type(\DateTimeInterface::class)]
    #[Assert\GreaterThanOrEqual("today", message: "Start date cannot be in the past.")]
    private ?\DateTimeInterface $startDate = null;

    #[Assert\Type(\DateTimeInterface::class)]
    #[Assert\GreaterThan(propertyPath: "startDate", message: "End date must be after the start date.")]
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $localisationGarage = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $price = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $delivery = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $available = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    private ?User $userOwner = null;





    public function getId(): ?int
    {
        return $this->id;
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

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getLocalisationGarage(): ?string
    {
        return $this->localisationGarage;
    }

    public function setLocalisationGarage(?string $localisationGarage): static
    {
        $this->localisationGarage = $localisationGarage;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDelivery(): ?string
    {
        return $this->delivery;
    }

    public function setDelivery(?string $delivery): static
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getAvailable(): ?string
    {
        return $this->available;
    }

    public function setAvailable(?string $available): static
    {
        $this->available = $available;

        return $this;
    }


    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

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
