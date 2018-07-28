<?php

namespace App\EventSubscriber;

use App\Entity\Job;
use Cocur\Slugify\Slugify;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class JobSubscriber implements EventSubscriber
{
    /**
     * @var Slugify $slugify
     */
    private $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @return Job|false
     */
    private function retrieveJob(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Job)
        {
            return $entity;
        }

        return false;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        if(!($job = $this->retrieveJob($args)))
        {
            return;
        }

        $this->slugifyJob($job);

        $this->setCreatedAt($job);
        $this->setUpdatedAt($job);
        $this->setExpiresAt($job);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        if(!($job = $this->retrieveJob($args)))
        {
            return;
        }

        $this->slugifyJob($job);

        $this->setUpdatedAt($job);
    }

    private function slugifyJob(Job $job)
    {
        $job->setCompanySlug($this->slugify->slugify($job->getCompany()));
        $job->setPositionSlug($this->slugify->slugify($job->getPosition()));
        $job->setLocationSlug($this->slugify->slugify($job->getLocation()));
    }

    private function setCreatedAt(Job $job)
    {
        if(!$job->getCreatedAt())
        {
            $job->setCreatedAt(new \DateTime());
        }
    }

    private function setUpdatedAt(Job $job)
    {
        $job->setUpdatedAt(new \DateTime());
    }

    private function setExpiresAt(Job $job)
    {
        if (!$job->getExpiresAt()) {
            $now = $job->getCreatedAt() ? $job->getCreatedAt()->format('U') : time();
            $job->setExpiresAt(new \DateTime(date('Y-m-d H:i:s', $now + 86400 * 30)));
        }
    }

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate'
        ];
    }
}
