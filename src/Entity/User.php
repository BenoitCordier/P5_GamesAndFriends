<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min:3,
        max:30,
        minMessage: "Votre nom d'utilisateur doit contenir plus de {{ limit }} lettres.",
        maxMessage: "Votre nom d'utilisateur doit contenir moins de {{ limit }} lettres.",
    )]
    private ?string $userName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $lastName = null;

    #[ORM\Column(name: 'email', type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email(
        message: 'Veuillez entrer un email valide.',
    )]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: 'players')]
    private Collection $games;

    #[ORM\OneToMany(mappedBy: 'eventAdmin', targetEntity: Event::class)]
    private Collection $adminEvents;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'eventPlayer')]
    private Collection $events;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->adminEvents = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): static
    {
        $this->userName = $userName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
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
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->addPlayer($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->games->removeElement($game)) {
            $game->removePlayer($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getAdminEvents(): Collection
    {
        return $this->adminEvents;
    }

    public function addAdminEvent(Event $adminEvent): static
    {
        if (!$this->adminEvents->contains($adminEvent)) {
            $this->adminEvents->add($adminEvent);
            $adminEvent->setEventAdmin($this);
        }

        return $this;
    }

    public function removeAdminEvent(Event $adminEvent): static
    {
        if ($this->adminEvents->removeElement($adminEvent)) {
            // set the owning side to null (unless already changed)
            if ($adminEvent->getEventAdmin() === $this) {
                $adminEvent->setEventAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->addEventPlayer($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            $event->removeEventPlayer($this);
        }

        return $this;
    }
}
