<?php

namespace App\Message;

class EmailNotification
{
    public function __construct(
        private string $recipient,
        private string $subject,
        private string $template,
        private array $context = []
    ) {
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}