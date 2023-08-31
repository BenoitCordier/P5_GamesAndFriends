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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $messageTitle = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $messageFrom = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $messageTo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $messageSendAt = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $messageText = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessageTitle(): ?string
    {
        return $this->messageTitle;
    }

    public function setMessageTitle(?string $messageTitle): static
    {
        $this->messageTitle = $messageTitle;

        return $this;
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

    public function getMessageSendAt(): ?\DateTimeImmutable
    {
        return $this->messageSendAt;
    }

    public function setMessageSendAt(\DateTimeImmutable $messageSendAt): static
    {
        $this->messageSendAt = $messageSendAt;

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
}
