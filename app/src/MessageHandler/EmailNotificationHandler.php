<?php

namespace App\MessageHandler;

use App\Message\EmailNotification;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EmailNotificationHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger
    ) {}

    public function __invoke(EmailNotification $notification): void
    {
        try {
            $context = $notification->getContext();
            $context['user_email'] = $context['user_email'] ?? $notification->getRecipient();

            $email = (new TemplatedEmail())
                ->to($notification->getRecipient())
                ->subject($notification->getSubject())
                ->htmlTemplate($notification->getTemplate())
                ->context($context);

            $this->mailer->send($email);
            $this->logger->info('Email sent successfully to ' . $notification->getRecipient());
        } catch (\Exception $e) {
            $this->logger->error('Email sending failed: ' . $e->getMessage());
            throw $e;
        }
    }
}