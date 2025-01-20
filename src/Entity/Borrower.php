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
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'borrower')]
    private Collection $reports;

    #[ORM\ManyToOne(inversedBy: 'reservedBy')]
    private ?Renting $reserved = null;

    public function __construct()
    {
        $this->reports = new ArrayCollection();
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
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setBorrower($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getBorrower() === $this) {
                $report->setBorrower(null);
            }
        }

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
}
