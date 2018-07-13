<?php

namespace App\Controller;

use App\Entity\Job;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class JobController extends Controller
{
    /**
     * @Route("/job", name="job")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $jobs = $em->getRepository(Job::class)->findAll();

        return $this->render('job/index.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * @Route("/job/{id}", name="job_show")
     * @ParamConverter()
     */
    public function show(Job $job)
    {
        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }
}
