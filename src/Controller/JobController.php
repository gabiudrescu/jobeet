<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Job;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class JobController extends Controller
{
    /**
     * @Route("/{category}", name="job_category", requirements={"category" = "\d+"})
     * @ParamConverter()
     * @Method("GET")
     */
    public function index(Category $category)
    {
        return $this->render('job/index.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/job/{company}/{location}/{id}/{position}", name="job_show", requirements={"id" = "\d+"})
     * @ParamConverter()
     * @Method("GET")
     */
    public function show(Job $job)
    {
        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }
}
