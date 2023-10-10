<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $messageFrom = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $messageTo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $messageDate = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $messageText = null;

    #[ORM\ManyToOne(inversedBy: 'message')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MessageThread $messageThread = null;

    public function __construct()
    {
        $this->messageDate = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessageFrom(): ?User
    {
        return $this->messageFrom;
    }

    public function setMessageFrom(?User $messageFrom): static
    {
        $this->messageFrom = $messageFrom;

        return $this;
    }

    public function getMessageTo(): ?User
    {
        return $this->messageTo;
    }

    public function setMessageTo(?User $messageTo): static
    {
        $this->messageTo = $messageTo;

        return $this;
    }

    public function getMessageDate(): ?\DateTimeImmutable
    {
        return $this->messageDate;
    }

    public function setMessageDate(\DateTimeImmutable $messageDate): static
    {
        $this->messageDate = $messageDate;

        return $this;
    }

    public function getMessageText(): ?string
    {
        return $this->messageText;
    }

    public function setMessageText(string $messageText): static
    {
        $this->messageText = $messageText;

        return $this;
    }

    public function getMessageThread(): ?MessageThread
    {
        return $this->messageThread;
    }

    public function setMessageThread(?MessageThread $messageThread): static
    {
        $this->messageThread = $messageThread;

        return $this;
    }
}
