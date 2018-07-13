<?php

namespace App\Controller;

use App\Entity\Job;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
     */
    public function show(int $id)
    {
        $em = $this->getDoctrine()->getManager();

        $job = $em->getRepository(Job::class)->find($id);

        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }
}
