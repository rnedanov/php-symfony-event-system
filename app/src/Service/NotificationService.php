<?php

namespace App\Service;

use App\Entity\SubscriptionType;
use App\Entity\User;
use App\Message\EmailNotification;
use Symfony\Component\Messenger\MessageBusInterface;

class NotificationService
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    public function sendSubscriptionNotification(User $user, SubscriptionType $subscriptionType): void
    {
        $this->bus->dispatch(new EmailNotification(
            $user->getEmail(),
            'Подтверждение подписки: ' . $subscriptionType->getName(),
            'emails/subscription_confirmation.html.twig',
            [
                'user' => $user,
                'user_email' => $user->getEmail(),
                'subscriptionType' => $subscriptionType,
            ]
        ));
    }
}