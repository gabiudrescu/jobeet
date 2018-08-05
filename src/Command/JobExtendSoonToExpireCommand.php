<?php

namespace App\Command;

use App\EventSubscriber\JobExtender;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class JobExtendSoonToExpireCommand extends Command
{

    /**
     * @var JobExtender
     */
    private $jobExtender;

    public function __construct(?string $name = null, JobExtender $jobExtender)
    {
        parent::__construct($name);
        $this->jobExtender = $jobExtender;
    }

    protected static $defaultName = 'job:extend-soon-to-expire';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addOption('days', null, InputOption::VALUE_REQUIRED, 'Extends job that expire in the next number of days', 5)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobs = $this->jobExtender->getJobsExpiringIn($input->getOption('days'));

        $this->jobExtender->extendJobsExpiringIn($jobs);
    }
}
