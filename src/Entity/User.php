<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['user'=>User::class, 'borrower'=>Borrower::class,
    'owner'=>Owner::class, 'jurist'=>Jurist::class, 'admin'=>Admin::class])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
abstract class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    /**
     * @var Collection<int, message>
     */
    #[ORM\OneToMany(targetEntity: message::class, mappedBy: 'user')]
    private Collection $message_snd;

    /**
     * @var Collection<int, message>
     */
    #[ORM\OneToMany(targetEntity: message::class, mappedBy: 'user')]
    private Collection $message_rcv;

    public function __construct()
    {
        $this->message_snd = new ArrayCollection();
        $this->message_rcv = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @return Collection<int, message>
     */
    public function getMessageSnd(): Collection
    {
        return $this->message_snd;
    }

    public function addMessageSnd(message $messageSnd): static
    {
        if (!$this->message_snd->contains($messageSnd)) {
            $this->message_snd->add($messageSnd);
            $messageSnd->setUser($this);
        }

        return $this;
    }

    public function removeMessageSnd(message $messageSnd): static
    {
        if ($this->message_snd->removeElement($messageSnd)) {
            // set the owning side to null (unless already changed)
            if ($messageSnd->getUser() === $this) {
                $messageSnd->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, message>
     */
    public function getMessageRcv(): Collection
    {
        return $this->message_rcv;
    }

    public function addMessageRcv(message $messageRcv): static
    {
        if (!$this->message_rcv->contains($messageRcv)) {
            $this->message_rcv->add($messageRcv);
            $messageRcv->setUser($this);
        }

        return $this;
    }

    public function removeMessageRcv(message $messageRcv): static
    {
        if ($this->message_rcv->removeElement($messageRcv)) {
            // set the owning side to null (unless already changed)
            if ($messageRcv->getUser() === $this) {
                $messageRcv->setUser(null);
            }
        }

        return $this;
    }
}
