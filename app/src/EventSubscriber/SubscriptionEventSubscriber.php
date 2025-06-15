<?php

namespace App\EventSubscriber;

use App\Event\SubscriptionCreatedEvent;
use App\Service\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubscriptionEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SubscriptionCreatedEvent::NAME => 'onSubscriptionCreated',
        ];
    }

    public function onSubscriptionCreated(SubscriptionCreatedEvent $event): void
    {
        $this->notificationService->sendSubscriptionNotification(
            $event->getUser(),
            $event->getSubscriptionType()
        );
    }
}
