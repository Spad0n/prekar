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

    #[ORM\OneToOne(mappedBy: 'basedOn', cascade: ['persist', 'remove'])]
    private ?Offer $offer = null;

    /**
     * @var Collection<int, Borrower>
     */
    #[ORM\OneToMany(targetEntity: Borrower::class, mappedBy: 'reserved')]
    private Collection $reservedBy;

    public function __construct()
    {
        $this->reservedBy = new ArrayCollection();
    }



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
        // unset the owning side of the relation if necessary
        if ($offer === null && $this->offer !== null) {
            $this->offer->setBasedOn(null);
        }

        // set the owning side of the relation if necessary
        if ($offer !== null && $offer->getBasedOn() !== $this) {
            $offer->setBasedOn($this);
        }

        $this->offer = $offer;

        return $this;
    }

    /**
     * @return Collection<int, Borrower>
     */
    public function getReservedBy(): Collection
    {
        return $this->reservedBy;
    }

    public function addReservedBy(Borrower $reservedBy): static
    {
        if (!$this->reservedBy->contains($reservedBy)) {
            $this->reservedBy->add($reservedBy);
            $reservedBy->setReserved($this);
        }

        return $this;
    }

    public function removeReservedBy(Borrower $reservedBy): static
    {
        if ($this->reservedBy->removeElement($reservedBy)) {
            // set the owning side to null (unless already changed)
            if ($reservedBy->getReserved() === $this) {
                $reservedBy->setReserved(null);
            }
        }

        return $this;
    }

}
