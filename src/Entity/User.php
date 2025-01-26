<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['user'=>User::class, 'jurist'=>Jurist::class, 'admin'=>Admin::class])]
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


    /**
     * Borrower informations
     */


    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startSub = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endSub = null;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'userBorrower')]
    private Collection $reportedBorrower;

    /**
     * @var Collection<int, Renting>
     */
    #[ORM\OneToMany(targetEntity: Renting::class, mappedBy: 'userBorrower')]
    private Collection $rentings;


    /**
     * Owner informations
     */


    #[ORM\Column(nullable: true)]
    private ?int $nbOffers = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $balance = null;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'userOwner')]
    private Collection $reportedOwner;

    /**
     * @var Collection<int, Car>
     */
    #[ORM\OneToMany(targetEntity: Car::class, mappedBy: 'userOwner')]
    private Collection $cars;

    /**
     * @var Collection<int, Offer>
     */
    #[ORM\OneToMany(targetEntity: Offer::class, mappedBy: 'userOwner')]
    private Collection $offers;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'userOwner')]
    private Collection $payments;


    public function __construct()
    {
        $this->message_snd = new ArrayCollection();
        $this->message_rcv = new ArrayCollection();
        $this->reportedBorrower = new ArrayCollection();
        $this->rentings = new ArrayCollection();
        $this->reportedOwner = new ArrayCollection();
        $this->cars = new ArrayCollection();
        $this->offers = new ArrayCollection();
        $this->payments = new ArrayCollection();
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

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
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

    /**
     * BORROWER
     */


    public function getStartSub(): ?\DateTimeInterface
    {
        return $this->startSub;
    }

    public function setStartSub(?\DateTimeInterface $startSub): static
    {
        $this->startSub = $startSub;

        return $this;
    }

    public function getEndSub(): ?\DateTimeInterface
    {
        return $this->endSub;
    }

    public function setEndSub(?\DateTimeInterface $endSub): static
    {
        $this->endSub = $endSub;

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReportedBorrower(): Collection
    {
        return $this->reportedBorrower;
    }

    public function addReportedBorrower(Report $reportedBorrower): static
    {
        if (!$this->reportedBorrower->contains($reportedBorrower)) {
            $this->reportedBorrower->add($reportedBorrower);
            $reportedBorrower->setUserBorrower($this);
        }

        return $this;
    }

    public function removeReportedBorrower(Report $reportedBorrower): static
    {
        if ($this->reportedBorrower->removeElement($reportedBorrower)) {
            // set the owning side to null (unless already changed)
            if ($reportedBorrower->getUserBorrower() === $this) {
                $reportedBorrower->setUserBorrower(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Renting>
     */
    public function getRentings(): Collection
    {
        return $this->rentings;
    }

    public function addRenting(Renting $renting): static
    {
        if (!$this->rentings->contains($renting)) {
            $this->rentings->add($renting);
            $renting->setUserBorrower($this);
        }

        return $this;
    }

    public function removeRenting(Renting $renting): static
    {
        if ($this->rentings->removeElement($renting)) {
            // set the owning side to null (unless already changed)
            if ($renting->getUserBorrower() === $this) {
                $renting->setUserBorrower(null);
            }
        }

        return $this;
    }


    /**
     * OWNER
     */

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

    /**
     * @return Collection<int, Report>
     */
    public function getReportedOwner(): Collection
    {
        return $this->reportedOwner;
    }

    public function addReportedOwner(Report $reportedOwner): static
    {
        if (!$this->reportedOwner->contains($reportedOwner)) {
            $this->reportedOwner->add($reportedOwner);
            $reportedOwner->setUserOwner($this);
        }

        return $this;
    }

    public function removeReportedOwner(Report $reportedOwner): static
    {
        if ($this->reportedOwner->removeElement($reportedOwner)) {
            // set the owning side to null (unless already changed)
            if ($reportedOwner->getUserOwner() === $this) {
                $reportedOwner->setUserOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Car>
     */
    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function addCar(Car $car): static
    {
        if (!$this->cars->contains($car)) {
            $this->cars->add($car);
            $car->setUserOwner($this);
        }

        return $this;
    }

    public function removeCar(Car $car): static
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getUserOwner() === $this) {
                $car->setUserOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Offer>
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): static
    {
        if (!$this->offers->contains($offer)) {
            $this->offers->add($offer);
            $offer->setUserOwner($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): static
    {
        if ($this->offers->removeElement($offer)) {
            // set the owning side to null (unless already changed)
            if ($offer->getUserOwner() === $this) {
                $offer->setUserOwner(null);
            }
        }

        return $this;
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
            $payment->setUserOwner($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getUserOwner() === $this) {
                $payment->setUserOwner(null);
            }
        }

        return $this;
    }
}