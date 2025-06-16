<?php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Worker;

#[AsCommand(
    name: 'app:consume-messages',
    description: 'Consume messages'
)]
class ConsumeMessagesCommand extends Command
{
    public function __construct(private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Consumes messages from the message queue');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $worker = new Worker([$this->bus], $this->bus);
        $worker->run();
        return Command::SUCCESS;
    }
}