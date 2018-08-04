<?php
/**
 * Created by PhpStorm.
 * User: gabiudrescu
 * Date: 04.08.2018
 * Time: 17:02
 */

namespace App\Form\DataTransformer;


use App\Entity\Job;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Translation\TranslatorInterface;

class TokenToJobTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager $entityManager
     */
    private $entityManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * TokenToJobTransformer constructor.
     *
     * @param ObjectManager       $entityManager
     * @param TranslatorInterface $translator
     */
    public function __construct(ObjectManager $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @param Job|null $job
     *
     * @return string
     */
    public function transform($job)
    {
        if(null === $job)
        {
            return '';
        }

        return $job->getToken();
    }

    public function reverseTransform($token)
    {
        if(!$token)
        {
            return;
        }

        $job = $this->entityManager->getRepository(Job::class)->findOneBy(['token' => $token]);

        if(null === $job)
        {
            throw new TransformationFailedException(
                $this->translator->trans('jobeet.job.publish.failed_transformation', ['%token%' => $token])
            );
        }

        return $job;
    }

}
