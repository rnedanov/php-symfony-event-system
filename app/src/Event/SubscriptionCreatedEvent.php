<?php

namespace App\Event;

use App\Entity\User;
use App\Entity\SubscriptionType;
use Symfony\Contracts\EventDispatcher\Event;

class SubscriptionCreatedEvent extends Event
{
    public const NAME = 'subscription.created';

    public function __construct(
        private User $user,
        private SubscriptionType $subscriptionType
    ) {}

    public function getUser(): User
    {
        return $this->user;
    }

    public function getSubscriptionType(): SubscriptionType
    {
        return $this->subscriptionType;
    }
}
