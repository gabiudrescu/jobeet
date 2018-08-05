<?php
/**
 * Created by PhpStorm.
 * User: gabiudrescu
 * Date: 05.08.2018
 * Time: 14:00
 */

namespace App\EventSubscriber;


use App\Entity\Job;
use App\Repository\JobRepository;

class JobExtender
{
    /**
     * @var JobRepository $jobRepository
     */
    private $jobRepository;

    /**
     * JobExtender constructor.
     *
     * @param JobRepository $jobRepository
     */
    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    public function extendJobsExpiringIn($jobs)
    {
        $this->extendJobs($jobs);
    }

    /**
     * @param int $days
     *
     * @return Job[]
     */
    public function getJobsExpiringIn(int $days) : iterable
    {
        return $this->jobRepository->findJobsExpiringIn($days);
    }

    /**
     * @param Job[] $jobs
     */
    private function extendJobs($jobs)
    {
        foreach ($jobs as $key => $job)
        {
            $job->extendWithDays(30);

            if($key % 10 === 0)
            {
                $this->jobRepository->flush();
            }
        }
    }
}
