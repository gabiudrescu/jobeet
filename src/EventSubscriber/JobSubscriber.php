<?php

namespace App\EventSubscriber;

use App\Entity\Job;
use Cocur\Slugify\Slugify;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class JobSubscriber implements EventSubscriber
{

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    private function index(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Job){
            $slugify = new Slugify();

            $entity->setCompanySlug($slugify->slugify($entity->getCompany()));
            $entity->setPositionSlug($slugify->slugify($entity->getPosition()));
            $entity->setLocationSlug($slugify->slugify($entity->getLocation()));
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
