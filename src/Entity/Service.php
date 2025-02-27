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


    #[ORM\OneToOne(mappedBy: 'apply', cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
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



    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): static
    {
        // unset the owning side of the relation if necessary
        if ($payment === null && $this->payment !== null) {
            $this->payment->setApply(null);
        }

        // set the owning side of the relation if necessary
        if ($payment !== null && $payment->getApply() !== $this) {
            $payment->setApply($this);
        }

        $this->payment = $payment;

        return $this;
    }

    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(?Admin $admin): static
    {
        $this->admin = $admin;

        return $this;
    }


}
