<?php

namespace App\Entity;

use App\Repository\JuristRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JuristRepository::class)]
class Jurist extends User
{
    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'jurist')]
    private Collection $report;

    public function __construct()
    {
        $this->report = new ArrayCollection();
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReport(): Collection
    {
        return $this->report;
    }

    public function addReport(Report $report): static
    {
        if (!$this->report->contains($report)) {
            $this->report->add($report);
            $report->setJurist($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->report->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getJurist() === $this) {
                $report->setJurist(null);
            }
        }

        return $this;
    }
}
