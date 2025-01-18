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
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'user_banned')]
    private Collection $banned;

    #[ORM\OneToOne(inversedBy: 'admin', cascade: ['persist', 'remove'])]
    private ?Service $configure = null;

    #[ORM\ManyToOne(inversedBy: 'admins')]
    private ?Payment $confirm = null;

    public function __construct()
    {
        $this->banned = new ArrayCollection();
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
}
