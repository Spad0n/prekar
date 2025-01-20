<?php

namespace App\Entity;

use App\Repository\OwnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OwnerRepository::class)]
class Owner extends User
{

    #[ORM\Column(nullable: true)]
    private ?int $nbOffers = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $balance = null;

    #[ORM\ManyToOne(inversedBy: 'askOwner')]
    private ?Payment $payment = null;


    #[ORM\ManyToOne(inversedBy: 'owners')]
    private ?Offer $publish = null;

    #[ORM\ManyToOne(inversedBy: 'owners')]
    private ?Car $Own = null;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'owner')]
    private Collection $reported;

    public function __construct()
    {
        parent::__construct();
        $this->reported = new ArrayCollection();
    }



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

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): static
    {
        $this->payment = $payment;

        return $this;
    }


    public function getPublish(): ?Offer
    {
        return $this->publish;
    }

    public function setPublish(?Offer $publish): static
    {
        $this->publish = $publish;

        return $this;
    }

    public function getOwn(): ?Car
    {
        return $this->Own;
    }

    public function setOwn(?Car $Own): static
    {
        $this->Own = $Own;

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReported(): Collection
    {
        return $this->reported;
    }

    public function addReported(Report $reported): static
    {
        if (!$this->reported->contains($reported)) {
            $this->reported->add($reported);
            $reported->setOwner($this);
        }

        return $this;
    }

    public function removeReported(Report $reported): static
    {
        if ($this->reported->removeElement($reported)) {
            // set the owning side to null (unless already changed)
            if ($reported->getOwner() === $this) {
                $reported->setOwner(null);
            }
        }

        return $this;
    }


}
