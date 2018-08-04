<?php

namespace App\EventSubscriber;

use App\Entity\Job;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class JobSubscriber implements EventSubscriber
{
    public function prePersist(LifecycleEventArgs $args)
    {
        if(!($job = $this->retrieveJob($args)))
        {
            return;
        }

        $this->setExpiresAt($job);
        $this->setHash($job);
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

    private function setHash(Job $job)
    {
        $job->setHash(base64_encode(random_bytes(50)));
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
