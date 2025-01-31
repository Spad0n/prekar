<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends User
{



    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'admin')]
    private Collection $paymentsAdmin;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'user_banned')]
    private Collection $banned;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\OneToMany(targetEntity: Service::class, mappedBy: 'admin')]
    private Collection $services;

    public function __construct()
    {
        parent::__construct();
        $this->paymentsAdmin = new ArrayCollection();
        $this->banned = new ArrayCollection();
        $this->services = new ArrayCollection();
    }





    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->paymentsAdmin;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->paymentsAdmin->contains($payment)) {
            $this->paymentsAdmin->add($payment);
            $payment->setAdmin($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->paymentsAdmin->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getAdmin() === $this) {
                $payment->setAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getBanned(): Collection
    {
        return $this->banned;
    }

    public function addBanned(User $banned): static
    {
        if (!$this->banned->contains($banned)) {
            $this->banned->add($banned);
        }

        return $this;
    }

    public function removeBanned(User $banned): static
    {
        $this->banned->removeElement($banned);

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setAdmin($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getAdmin() === $this) {
                $service->setAdmin(null);
            }
        }

        return $this;
    }
}
