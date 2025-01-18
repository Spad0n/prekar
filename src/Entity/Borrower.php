<?php

namespace App\Entity;

use App\Repository\BorrowerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BorrowerRepository::class)]
class Borrower extends User
{

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startSub = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endSub = null;

    /**
     * @var Collection<int, Renting>
     */
    #[ORM\ManyToMany(targetEntity: Renting::class, mappedBy: 'reservedBy')]
    private Collection $rentings;

    public function __construct()
    {
        $this->rentings = new ArrayCollection();
    }


    public function getStartSub(): ?\DateTimeInterface
    {
        return $this->startSub;
    }

    public function setStartSub(?\DateTimeInterface $startSub): static
    {
        $this->startSub = $startSub;

        return $this;
    }

    public function getEndSub(): ?\DateTimeInterface
    {
        return $this->endSub;
    }

    public function setEndSub(?\DateTimeInterface $endSub): static
    {
        $this->endSub = $endSub;

        return $this;
    }

    /**
     * @return Collection<int, Renting>
     */
    public function getRentings(): Collection
    {
        return $this->rentings;
    }

    public function addRenting(Renting $renting): static
    {
        if (!$this->rentings->contains($renting)) {
            $this->rentings->add($renting);
            $renting->addReservedBy($this);
        }

        return $this;
    }

    public function removeRenting(Renting $renting): static
    {
        if ($this->rentings->removeElement($renting)) {
            $renting->removeReservedBy($this);
        }

        return $this;
    }
}
