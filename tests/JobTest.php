<?php

namespace App\Tests;

use App\Entity\Job;
use PHPUnit\Framework\TestCase;

class JobTest extends TestCase
{
    public function testJobExpiresAfter30days()
    {
        $job = new Job();

        $future = new \DateTime('+31 day');

        $this->assertLessThan($future, $job->getExpiresAt());
    }

    public function testJobDoesNotExpireAfter20Days()
    {
        $job = new Job();

        $future = new \DateTime('+20 day');

        $this->assertGreaterThan($future, $job->getExpiresAt());
    }
}
