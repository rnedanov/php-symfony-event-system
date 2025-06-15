<?php

namespace App\Entity;

use App\Repository\UserSubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSubscriptionRepository::class)]
class UserSubscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userSubscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: SubscriptionType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?SubscriptionType $subscriptionType = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $subscribedAt = null;

    public function __construct()
    {
        $this->subscribedAt = new \DateTime();
    }

    // Геттеры и сеттеры...
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getSubscriptionType(): ?SubscriptionType
    {
        return $this->subscriptionType;
    }

    public function setSubscriptionType(?SubscriptionType $subscriptionType): static
    {
        $this->subscriptionType = $subscriptionType;
        return $this;
    }

    public function getSubscribedAt(): ?\DateTimeInterface
    {
        return $this->subscribedAt;
    }

    public function setSubscribedAt(\DateTimeInterface $subscribedAt): static
    {
        $this->subscribedAt = $subscribedAt;
        return $this;
    }
}