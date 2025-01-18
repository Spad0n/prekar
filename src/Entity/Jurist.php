<?php

namespace App\Entity;

use App\Repository\JuristRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JuristRepository::class)]
class Jurist extends User
{
    #[ORM\ManyToOne(inversedBy: 'jurist')]
    private ?Dispute $manage = null;

    public function getManage(): ?Dispute
    {
        return $this->manage;
    }

    public function setManage(?Dispute $manage): static
    {
        $this->manage = $manage;

        return $this;
    }
}
