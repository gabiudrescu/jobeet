<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Job;

class HomepageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        $jobs = $this->getDoctrine()->getRepository(Job::class)->findAll();

        return $this->render('homepage/index.html.twig', [
            'category' => $jobs[0]->getCategory(),
            'jobs' => $jobs,
        ]);
    }
}
