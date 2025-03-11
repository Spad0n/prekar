<?php

namespace App\Entity;

use App\Repository\DisputeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DisputeRepository::class)]
class Dispute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $reportingDate = null;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'dispute', orphanRemoval: true)]
    private Collection $reports;

    #[ORM\ManyToOne(inversedBy: 'disputes')]
    private ?Jurist $jurist = null;


    public function __construct()
    {
        $this->reports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getReportingDate(): ?\DateTimeInterface
    {
        return $this->reportingDate;
    }

    public function setReportingDate(\DateTimeInterface $reportingDate): static
    {
        $this->reportingDate = $reportingDate;

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
            $report->setDispute($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getDispute() === $this) {
                $report->setDispute(null);
            }
        }

        return $this;
    }

    public function getJurist(): ?Jurist
    {
        return $this->jurist;
    }

    public function setJurist(?Jurist $jurist): static
    {
        $this->jurist = $jurist;

        return $this;
    }


}
