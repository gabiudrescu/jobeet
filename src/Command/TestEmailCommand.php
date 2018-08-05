<?php

namespace App\Command;

use App\Entity\Job;
use App\EventSubscriber\EmailSubscriber;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class TestEmailCommand extends Command
{

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(?string $name = null, EventDispatcherInterface $dispatcher)
    {
        parent::__construct($name);
        $this->dispatcher = $dispatcher;
    }

    protected static $defaultName = 'test:email';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $job = new Job();

        $job->setEmail('gabriel.udr@gmail.com');
        $job->setCompany('BestValue');
        $job->setPosition('PM');
        $job->setToken('blabla');

        $this->dispatcher->dispatch(EmailSubscriber::SEND_JOB_PREVIEW_LINK, new GenericEvent($job));
    }
}
