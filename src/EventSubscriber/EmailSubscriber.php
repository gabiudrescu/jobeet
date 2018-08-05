<?php
/**
 * Created by PhpStorm.
 * User: gabiudrescu
 * Date: 04.08.2018
 * Time: 17:42
 */

namespace App\EventSubscriber;


use App\Entity\Job;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class EmailSubscriber implements EventSubscriberInterface
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var EngineInterface
     */
    private $templateEngine;

    public function __construct(
        \Swift_Mailer $mailer,
        TranslatorInterface $translator,
        EngineInterface $templateEngine
    ) {
        $this->mailer = $mailer;
        $this->trans = $translator;
        $this->templateEngine = $templateEngine;
    }

    const SEND_JOB_PREVIEW_LINK = 'send_job_preview_link';

    public static function getSubscribedEvents()
    {
        return [
            self::SEND_JOB_PREVIEW_LINK => [
                'sendJobPreviewLink'
            ]
        ];
    }

    public function sendJobPreviewLink(GenericEvent $event)
    {
        /**
         * @var Job $job
         */
        $job = $event->getSubject();

        $message = (new \Swift_Message($this->trans('jobeet.email.new.subject')))
            ->setFrom($this->trans('jobeet.email.new.from.email'), $this->trans('jobeet.email.new.from.name'))
            ->setTo($job->getEmail())
            ->setBody(
                $this->templateEngine->render(
                    'email/new/new.mjml.twig',
                    [
                        'job' => $job
                    ]
                ),
                'text/mjml'
            );
        $this->mailer->send($message);
    }

    private function trans(string $key)
    {
        return $this->trans->trans($key);
    }
}
