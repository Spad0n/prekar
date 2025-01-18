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
}
