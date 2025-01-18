<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'dispute')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Jurist $jurist = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dispute $dispute = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Owner $owner = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDispute(): ?Dispute
    {
        return $this->dispute;
    }

    public function setDispute(?Dispute $dispute): static
    {
        $this->dispute = $dispute;

        return $this;
    }

    public function getOwner(): ?Owner
    {
        return $this->owner;
    }

    public function setOwner(?Owner $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
