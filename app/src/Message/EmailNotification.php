<?php

namespace App\Message;

class EmailNotification
{
    private string $recipient;
    private string $subject;
    private string $template;
    private array $context;

    public function __construct(string $recipient, string $subject, string $template, array $context = [])
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->template = $template;
        $this->context = $context;
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