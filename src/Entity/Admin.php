<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends User
{

    #[ORM\OneToOne(inversedBy: 'admin', cascade: ['persist', 'remove'])]
    private ?Service $configure = null;

    #[ORM\ManyToOne(inversedBy: 'admins')]
    private ?Payment $confirm = null;

    #[ORM\ManyToOne]
    private ?User $banned = null;


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

    public function getBanned(): ?User
    {
        return $this->banned;
    }

    public function setBanned(?User $banned): static
    {
        $this->banned = $banned;

        return $this;
    }
}
