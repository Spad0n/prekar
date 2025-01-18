<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(length: 50)]
    private ?string $brand = null;

    #[ORM\Column(length: 50)]
    private ?string $model = null;

    #[ORM\Column(length: 20)]
    private ?string $registration = null;

    private ?int $year = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbSeat = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $bootCapacity = null;

    #[ORM\Column(length: 20)]
    private ?string $fuelType = null;

    /**
     * @var Collection<int, Owner>
     */
    #[ORM\OneToMany(targetEntity: Owner::class, mappedBy: 'Own')]
    private Collection $owners;

    #[ORM\ManyToOne(inversedBy: 'relateTo')]
    private ?Offer $offer = null;

    public function __construct()
    {
        $this->owners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getRegistration(): ?string
    {
        return $this->registration;
    }

    public function setRegistration(?string $registration): static
    {
        $this->registration = $registration;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getNbSeat(): ?int
    {
        return $this->nbSeat;
    }

    public function setNbSeat(?int $nbSeat): static
    {
        $this->nbSeat = $nbSeat;

        return $this;
    }

    public function getBootCapacity(): ?string
    {
        return $this->bootCapacity;
    }

    public function setBootCapacity(?string $bootCapacity): static
    {
        $this->bootCapacity = $bootCapacity;

        return $this;
    }

    public function getFuelType(): ?string
    {
        return $this->fuelType;
    }

    public function setFuelType(?string $fuelType): static
    {
        $this->fuelType = $fuelType;

        return $this;
    }

    /**
     * @return Collection<int, Owner>
     */
    public function getOwners(): Collection
    {
        return $this->owners;
    }

    public function addOwner(Owner $owner): static
    {
        if (!$this->owners->contains($owner)) {
            $this->owners->add($owner);
            $owner->setOwn($this);
        }

        return $this;
    }

    public function removeOwner(Owner $owner): static
    {
        if ($this->owners->removeElement($owner)) {
            // set the owning side to null (unless already changed)
            if ($owner->getOwn() === $this) {
                $owner->setOwn(null);
            }
        }

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
}
