<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends User
{

    #[ORM\OneToOne(inversedBy: 'admin', cascade: ['persist', 'remove'])]
    private ?Service $configure = null;

    #[ORM\ManyToOne(inversedBy: 'admins')]
    private ?Payment $confirm = null;

    #[ORM\ManyToOne]
    private ?user $banned = null;


    public function getConfigure(): ?Service
    {
        return $this->configure;
    }

    public function setConfigure(?Service $configure): static
    {
        $this->configure = $configure;

        return $this;
    }

    public function getConfirm(): ?Payment
    {
        return $this->confirm;
    }

    public function setConfirm(?Payment $confirm): static
    {
        $this->confirm = $confirm;

        return $this;
    }

    public function getBanned(): ?user
    {
        return $this->banned;
    }

    public function setBanned(?user $banned): static
    {
        $this->banned = $banned;

        return $this;
    }
}
