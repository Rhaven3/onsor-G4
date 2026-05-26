<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\StateEnum;
use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(min: 3, max: 255, minMessage: 'le nom de la Sortie doit faire minimum 3 charactères', maxMessage: 'le nom de la Sortie doit faire maximum 255 charactères')]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\GreaterThanOrEqual('now', message: 'La date de début doit être égal ou supérieur à la date d\'aujourd\'hui')]
    #[ORM\Column]
    private ?\DateTime $startDate = null;

    #[Assert\GreaterThan(null, 'startDate', message: 'La date de fin doit être supérieur à la date de début')]
    #[ORM\Column]
    private ?\DateTime $endDate = null;

    #[Assert\LessThanOrEqual(null, 'endDate', message: 'la date limite d\'inscription doit être inférieur ou égal à la date de fin')]
    #[ORM\Column(nullable: true)]
    private ?\DateTime $limitRegistrationDate = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'createdTrips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $organisator = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'participatedTrips')]
    private Collection $participants;
    private $id1;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    #[ORM\ManyToOne(inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    #[ORM\ManyToOne(inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\Column(nullable: true, enumType: StateEnum::class)]
    private ?StateEnum $state = null;

    #[Assert\GreaterThan(0, message: 'le nombre maximum d\'inscription doit être supérieur à 0')]
    #[ORM\Column]
    private ?int $maxRegistration = null;

    public function getId(): ?int
    {
        $this->id1 = $this->id;
        return $this->id1;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getLimitRegistrationDate(): ?\DateTime
    {
        return $this->limitRegistrationDate;
    }

    public function setLimitRegistrationDate(?\DateTime $limitRegistrationDate): static
    {
        $this->limitRegistrationDate = $limitRegistrationDate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOrganisator(): ?User
    {
        return $this->organisator;
    }

    public function setOrganisator(?User $organisator): static
    {
        $this->organisator = $organisator;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addParticipatedTrip($this);
        }
        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeParticipatedTrip($this);
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getState(): ?StateEnum
    {
        return $this->state;
    }

    public function setState(?StateEnum $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getMaxRegistration(): ?int
    {
        return $this->maxRegistration;
    }

    public function setMaxRegistration(int $maxRegistration): static
    {
        $this->maxRegistration = $maxRegistration;

        return $this;
    }
}
