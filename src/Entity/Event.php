<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[UniqueEntity('name')]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min:3,
        max:100,
        minMessage: "Le nom de l'évènement doit contenir plus de {{ limit }} lettres.",
        maxMessage: "Le nom de l'évènement doit contenir moins de {{ limit }} lettres."
    )]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Game $eventGame = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $eventDescription = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    private ?\DateTime $eventStartAt = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    private ?\DateTime $eventEndAt = null;

    #[ORM\ManyToOne(inversedBy: 'adminEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $eventAdmin = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'events')]
    private Collection $eventPlayers;

    #[ORM\Column]
    #[Assert\NotNull()]
    #[Assert\Positive]
    #[Assert\GreaterThanOrEqual(2)]
    private ?int $eventMaxPlayer = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $location = null;

    public function __construct()
    {
        $this->eventPlayers = new ArrayCollection();
        $this->eventStartAt = new \DateTime();
        $this->eventEndAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEventGame(): ?Game
    {
        return $this->eventGame;
    }

    public function setEventGame(?Game $eventGame): static
    {
        $this->eventGame = $eventGame;

        return $this;
    }

    public function getEventDescription(): ?string
    {
        return $this->eventDescription;
    }

    public function setEventDescription(string $eventDescription): static
    {
        $this->eventDescription = $eventDescription;

        return $this;
    }

    public function getEventStartAt(): ?\DateTime
    {
        return $this->eventStartAt;
    }

    public function setEventStartAt(\DateTime $eventStartAt): static
    {
        $this->eventStartAt = $eventStartAt;

        return $this;
    }

    public function getEventEndAt(): ?\DateTime
    {
        return $this->eventEndAt;
    }

    public function setEventEndAt(\DateTime $eventEndAt): static
    {
        $this->eventEndAt = $eventEndAt;

        return $this;
    }

    public function getEventAdmin(): ?User
    {
        return $this->eventAdmin;
    }

    public function setEventAdmin(?User $eventAdmin): static
    {
        $this->eventAdmin = $eventAdmin;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getEventPlayers(): Collection
    {
        return $this->eventPlayers;
    }

    public function addEventPlayer(User $eventPlayer): static
    {
        if (!$this->eventPlayers->contains($eventPlayer)) {
            $this->eventPlayers->add($eventPlayer);
        }

        return $this;
    }

    public function removeEventPlayer(User $eventPlayer): static
    {
        $this->eventPlayers->removeElement($eventPlayer);

        return $this;
    }

    public function getEventMaxPlayer(): ?int
    {
        return $this->eventMaxPlayer;
    }

    public function setEventMaxPlayer(int $eventMaxPlayer): static
    {
        $this->eventMaxPlayer = $eventMaxPlayer;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }
}
