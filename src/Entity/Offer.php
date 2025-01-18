<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

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

    /**
     * @var Collection<int, Owner>
     */
    #[ORM\OneToMany(targetEntity: Owner::class, mappedBy: 'publish')]
    private Collection $owners;

    public function __construct()
    {
        $this->owners = new ArrayCollection();
    }

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
            $owner->setPublish($this);
        }

        return $this;
    }

    public function removeOwner(Owner $owner): static
    {
        if ($this->owners->removeElement($owner)) {
            // set the owning side to null (unless already changed)
            if ($owner->getPublish() === $this) {
                $owner->setPublish(null);
            }
        }

        return $this;
    }
}
