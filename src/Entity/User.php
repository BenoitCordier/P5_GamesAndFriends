<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email')]
#[UniqueEntity('name')]
#[ORM\EntityListeners(['App\EntityListener\UserListener'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min:3,
        max:180
    )]
    #[Assert\Email()]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    private array $roles = [];

    private ?string $plainPassword = null;
    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank]
    private ?string $password = 'password';

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min:3,
        max:50,
        minMessage: "Votre nom d'utilisateur doit contenir plus de {{ limit }} caractères.",
        maxMessage: "Votre nom d'utilisateur doit contenir moins de {{ limit }} caractères."
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min:2,
        max:255
    )]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min:2,
        max:255
    )]
    private ?string $lastName = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToMany(targetEntity: Game::class, inversedBy: 'users')]
    private Collection $games;

    #[ORM\Column]
    #[Assert\NotNull()]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $location = null;

    #[ORM\OneToMany(mappedBy: 'firstUser', targetEntity: MessageThread::class, orphanRemoval: true)]
    private Collection $firstUser;

    #[ORM\OneToMany(mappedBy: 'secondUser', targetEntity: MessageThread::class, orphanRemoval: true)]
    private Collection $secondUser;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->games = new ArrayCollection();
        $this->firstUser = new ArrayCollection();
        $this->secondUser = new ArrayCollection();
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
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        $this->games->removeElement($game);

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

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

    /**
     * @return Collection<int, MessageThread>
     */
    public function getFirstUser(): Collection
    {
        return $this->firstUser;
    }

    public function addFirstUser(MessageThread $firstUser): static
    {
        if (!$this->firstUser->contains($firstUser)) {
            $this->firstUser->add($firstUser);
            $firstUser->setFirstUser($this);
        }

        return $this;
    }

    public function removeFirstUser(MessageThread $firstUser): static
    {
        if ($this->firstUser->removeElement($firstUser)) {
            // set the owning side to null (unless already changed)
            if ($firstUser->getFirstUser() === $this) {
                $firstUser->setFirstUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MessageThread>
     */
    public function getSecondUser(): Collection
    {
        return $this->secondUser;
    }

    public function addSecondUser(MessageThread $secondUser): static
    {
        if (!$this->secondUser->contains($secondUser)) {
            $this->secondUser->add($secondUser);
            $secondUser->setSecondUser($this);
        }

        return $this;
    }

    public function removeSecondUser(MessageThread $secondUser): static
    {
        if ($this->secondUser->removeElement($secondUser)) {
            // set the owning side to null (unless already changed)
            if ($secondUser->getSecondUser() === $this) {
                $secondUser->setSecondUser(null);
            }
        }

        return $this;
    }

}
