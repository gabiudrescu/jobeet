<?php

namespace App\Controller;

use App\Entity\Category;
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
        /**
         * @var Category[] $categories
         */
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        array_walk($categories, function (Category $category){
            $category->setJobs($this->getDoctrine()->getRepository(Job::class)->findJobsForCategory($category));
        });

        return $this->render('homepage/index.html.twig', [
            'categories' => $categories
        ]);
    }
}
