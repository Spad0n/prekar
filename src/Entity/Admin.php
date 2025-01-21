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
    private Collection $payments;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'user_banned')]
    private Collection $banned;

    public function __construct()
    {
        parent::__construct();
        $this->payments = new ArrayCollection();
        $this->banned = new ArrayCollection();
    }





    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setAdmin($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
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
}
