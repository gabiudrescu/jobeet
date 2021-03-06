<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Job;
use App\EventSubscriber\EmailSubscriber;
use App\Form\JobEditType;
use App\Form\JobType;
use App\Form\PublishType;
use App\Repository\JobRepository;
use Doctrine\ORM\Query\Expr;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Webmozart\Assert\Assert;

class JobController extends Controller
{

    /**
     * @var JobRepository
     */
    private $jobRepository;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(JobRepository $jobRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->jobRepository = $jobRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/jobs-for/{category}", name="job_category")
     * @ParamConverter("category", options={"mapping": { "category":"slug"}})
     * @Method("GET")
     */
    public function index(Category $category)
    {
        /**
         * @var Pagerfanta $jobs
         */
        $jobs = $this->jobRepository->createPaginatorForJobsByCategory($category);

        return $this->render('job/index.html.twig', [
            'category' => $category,
            'jobs' => $jobs,
        ]);
    }

    /**
     * @Route("/job/{company}/{location}/{id}/{position}", name="job_show", requirements={"id" = "\d+"})
     * @ParamConverter()
     * @Method("GET")
     */
    public function show(Job $job)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('job/show.html.twig', [
            'job' => $job,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/preview/{token}", name="job_preview")
     * @ParamConverter("job", options={"mapping": { "token":"token"}})
     * @Method("GET")
     */
    public function preview(Job $job)
    {
        $publishForm = $this->createPublishForm($job);

        $editForm = $this->createGenerateEditForm($job);

        return $this->render('job/preview.html.twig', [
            'job' => $job,
            'publishForm' => $publishForm->createView(),
            'editForm' => $editForm->createView(),
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

            $job->setActivated(false);

            $em->persist($job);
            $em->flush();

            $this->eventDispatcher->dispatch(EmailSubscriber::SEND_JOB_PREVIEW_LINK, new GenericEvent($job));

            return $this->redirectToRoute('job_afterCreate', [
                'token' => $job->getToken()
            ]);
        }

        return $this->render('job/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/job/publish", name="job_publish")
     * @Method({"GET","POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function publish(Request $request)
    {
        $job = $this->getJobFromRequestForm($request);

        $form = $this->createPublishForm($job);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $job->setActivated(true);

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

    /**
     * @Route("/generate_edit/{token}", name="job_generate_edit")
     * @ParamConverter("job", options={"mapping": { "token":"token"}})
     * @Method("GET")
     */
    public function generateEditEmail(Job $job)
    {
        $this->eventDispatcher->dispatch(EmailSubscriber::SEND_JOB_EDIT_LINK, new GenericEvent($job));

        $flash = $this->get('translator')->trans('jobeet.job.create.after',[
            '%email%' =>  $job->getEmail(),
        ]);

        $this->get('session')->getFlashBag()->add('success', $flash);

        return $this->redirectToRoute('job_show', [
            'id' => $job->getId(),
            'company' => $job->getCompanySlug(),
            'location' => $job->getLocationSlug(),
            'position'=> $job->getPositionSlug(),
        ]);
    }

    /**
     * @Route("/job/edit", name="job_edit")
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request)
    {
        $job = $this->getJobFromRequestForm($request);

        $form = $this->createJobEditForm($job);

        return $this->render('job/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/job/handle_edit/{token}", name="job_handle_edit")
     * @ParamConverter("job", options={"mapping": { "token":"token"}})
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function handleEdit(Job $job, Request $request)
    {
        $form = $this->createJobEditForm($job);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if(true === $form->get('extend')->getData())
            {
                $job->extendWithDays(30);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('job_show', [
                'id' => $job->getId(),
                'company' => $job->getCompanySlug(),
                'location' => $job->getLocationSlug(),
                'position' => $job->getPositionSlug(),
            ]);
        }

        return $this->render('job/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/search", name="job_search")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function search(Request $request)
    {
        /**
         * @var string $query
         */
        $query = $request->get('q');

        Assert::notNull($query);

        $jobs = $this->jobRepository->createPaginatorForSearch($query);

        return $this->render('job/search.html.twig', [
            'query' => $query,
            'jobs' => $jobs
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

    /**
     * @param Job $job
     *
     * @return FormInterface
     */
    private function createPublishForm(Job $job): FormInterface
    {
        $publishForm = $this->createForm(
            PublishType::class,
            ['job' => $job],
            [
                'action' => $this->generateUrl('job_publish'),
                'method' => 'POST',
            ]
        );

        $publishForm->add(
            'publish',
            SubmitType::class,
            [
                'label' => 'jobeet.job.publish'
            ]
        );

        return $publishForm;
}

    /**
     * @param Job $job
     *
     * @return FormInterface
     */
    private function createGenerateEditForm(Job $job): FormInterface
    {
        $editForm = $this->createForm(
            PublishType::class,
            ['job' => $job],
            [
                'action' => $this->generateUrl('job_edit'),
                'method' => 'POST',
            ]
        );

        $editForm->add(
            'edit',
            SubmitType::class,
            [
                'label' => 'jobeet.job.edit'
            ]
        );

        return $editForm;
    }

    /**
     * @param Job $job
     *
     * @return FormInterface
     */
    private function createJobEditForm(Job $job): FormInterface
    {
        $editForm = $this->createForm(JobEditType::class, $job, [
                'action' => $this->generateUrl('job_handle_edit', ['token' => $job->getToken()]),
                'method' => 'POST',
            ]
        );

        $editForm->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'jobeet.job.save'
            ]
        );

        return $editForm;
    }

    /**
     * @param Request $request
     *
     * @return Job|null
     */
    private function getJobFromRequestForm(Request $request)
    {
        $data = $request->get('publish');

        Assert::isArray($data);
        Assert::keyExists($data, 'job');
        Assert::string($data['job']);

        $token = $data['job'];

        return $this->jobRepository->findOneBy(['token' => $token]);
    }
}
