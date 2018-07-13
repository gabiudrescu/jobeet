<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Job;
use App\Form\JobType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/job/new", name="job_new")
     * @Method({"GET","POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $job = new Job();

        $form = $this->createJobType($job);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('job_show', [
                'id' => $job->getId(),
                'company' => $job->getCompanySlug(),
                'location' => $job->getLocationSlug(),
                'position' => $job->getPositionSlug(),
            ]);
        }

        return $this->render('job/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function createJobType(Job $job) : FormInterface
    {
        $form = $this->createForm(JobType::class, $job, [
            'action' => $this->generateUrl('job_new'),
            'method' => 'POST',
        ]);
        $form->add('submit', SubmitType::class);

        return $form;
    }
}
