<?php

namespace App\Entity;

use App\Repository\OwnerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OwnerRepository::class)]
class Owner extends User
{

    #[ORM\Column(nullable: true)]
    private ?int $nbOffers = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $balance = null;

    public function getNbOffers(): ?int
    {
        return $this->nbOffers;
    }

    public function setNbOffers(?int $nbOffers): static
    {
        $this->nbOffers = $nbOffers;

        return $this;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(?string $balance): static
    {
        $this->balance = $balance;

        return $this;
    }
}
