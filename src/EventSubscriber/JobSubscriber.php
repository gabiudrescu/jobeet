<?php

namespace App\EventSubscriber;

use App\Entity\Job;
use Cocur\Slugify\Slugify;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class JobSubscriber implements EventSubscriber
{
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

        $this->setExpiresAt($job);
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
            'prePersist'
        ];
    }
}
