<?php

namespace App\Entity;

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
class User implements UserInterface, PasswordAuthenticatedUserInterface
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
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class,mappedBy: 'sender')]
    private Collection $message_snd;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class,mappedBy: 'receiver')]
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
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }


    public function addRole(string $role): static
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

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
        return $this->lastName;
    }

    public function setNom(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->name;
    }

    public function setPrenom(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessageSnd(): Collection
    {
        return $this->message_snd;
    }

    public function addMessageSnd(Message $message): static
    {
        if (!$this->message_snd->contains($message)) {
            $this->message_snd->add($message);
            $message->setIdSender($this);
        }

        return $this;
    }

    public function removeMessageSnd(Message $message): static
    {
        if ($this->message_snd->removeElement($message)) {
            if ($message->getIdSender() === $this) {
                $message->setIdSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessageRcv(): Collection
    {
        return $this->message_rcv;
    }

    public function addMessageRcv(Message $message): static
    {
        if (!$this->message_rcv->contains($message)) {
            $this->message_rcv->add($message);
            $message->setIdReceiver($this);
        }

        return $this;
    }

    public function removeMessageRcv(Message $message): static
    {
        if ($this->message_rcv->removeElement($message)) {
            if ($message->getIdReceiver() === $this) {
                $message->setIdReceiver(null);
            }
        }

        return $this;
    }
}