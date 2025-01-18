<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $payDate = null;

    /**
     * @var Collection<int, Admin>
     */
    #[ORM\OneToMany(targetEntity: Admin::class, mappedBy: 'confirm')]
    private Collection $admins;

    #[ORM\OneToOne(inversedBy: 'payment', cascade: ['persist', 'remove'])]
    private ?Service $apply = null;

    /**
     * @var Collection<int, Owner>
     */
    #[ORM\OneToMany(targetEntity: Owner::class, mappedBy: 'payment')]
    private Collection $askOwner;

    public function __construct()
    {
        $this->admins = new ArrayCollection();
        $this->askOwner = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPayDate(): ?\DateTimeInterface
    {
        return $this->payDate;
    }

    public function setPayDate(\DateTimeInterface $payDate): static
    {
        $this->payDate = $payDate;

        return $this;
    }

    /**
     * @return Collection<int, Admin>
     */
    public function getAdmins(): Collection
    {
        return $this->admins;
    }

    public function addAdmin(Admin $admin): static
    {
        if (!$this->admins->contains($admin)) {
            $this->admins->add($admin);
            $admin->setConfirm($this);
        }

        return $this;
    }

    public function removeAdmin(Admin $admin): static
    {
        if ($this->admins->removeElement($admin)) {
            // set the owning side to null (unless already changed)
            if ($admin->getConfirm() === $this) {
                $admin->setConfirm(null);
            }
        }

        return $this;
    }

    public function getApply(): ?Service
    {
        return $this->apply;
    }

    public function setApply(?Service $apply): static
    {
        $this->apply = $apply;

        return $this;
    }

    /**
     * @return Collection<int, Owner>
     */
    public function getAskOwner(): Collection
    {
        return $this->askOwner;
    }

    public function addAskOwner(Owner $askOwner): static
    {
        if (!$this->askOwner->contains($askOwner)) {
            $this->askOwner->add($askOwner);
            $askOwner->setPayment($this);
        }

        return $this;
    }

    public function removeAskOwner(Owner $askOwner): static
    {
        if ($this->askOwner->removeElement($askOwner)) {
            // set the owning side to null (unless already changed)
            if ($askOwner->getPayment() === $this) {
                $askOwner->setPayment(null);
            }
        }

        return $this;
    }
}
