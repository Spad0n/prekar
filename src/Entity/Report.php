<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dispute $dispute = null;



    #[ORM\ManyToOne(inversedBy: 'reportedBorrower')]
    private ?User $userBorrower = null;

    #[ORM\ManyToOne(inversedBy: 'reportedOwner')]
    private ?User $userOwner = null;


    public function getId(): ?int
    {
        return $this->id;
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



    public function getUserBorrower(): ?User
    {
        return $this->userBorrower;
    }

    public function setUserBorrower(?User $userBorrower): static
    {
        $this->userBorrower = $userBorrower;

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
