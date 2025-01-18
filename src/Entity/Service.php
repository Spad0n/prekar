<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $serviceFee = null;

    #[ORM\OneToOne(mappedBy: 'configure', cascade: ['persist', 'remove'])]
    private ?Admin $admin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceFee(): ?string
    {
        return $this->serviceFee;
    }

    public function setServiceFee(?string $serviceFee): static
    {
        $this->serviceFee = $serviceFee;

        return $this;
    }

    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(?Admin $admin): static
    {
        // unset the owning side of the relation if necessary
        if ($admin === null && $this->admin !== null) {
            $this->admin->setConfigure(null);
        }

        // set the owning side of the relation if necessary
        if ($admin !== null && $admin->getConfigure() !== $this) {
            $admin->setConfigure($this);
        }

        $this->admin = $admin;

        return $this;
    }
}
