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



    #[ORM\ManyToOne(inversedBy: 'reservedBy')]
    private ?Renting $reserved = null;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'borrower')]
    private Collection $reported;

    public function __construct()
    {
        parent::__construct();
        $this->reported = new ArrayCollection();
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



    public function getReserved(): ?Renting
    {
        return $this->reserved;
    }

    public function setReserved(?Renting $reserved): static
    {
        $this->reserved = $reserved;

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReported(): Collection
    {
        return $this->reported;
    }

    public function addReported(Report $reported): static
    {
        if (!$this->reported->contains($reported)) {
            $this->reported->add($reported);
            $reported->setBorrower($this);
        }

        return $this;
    }

    public function removeReported(Report $reported): static
    {
        if ($this->reported->removeElement($reported)) {
            // set the owning side to null (unless already changed)
            if ($reported->getBorrower() === $this) {
                $reported->setBorrower(null);
            }
        }

        return $this;
    }

}
