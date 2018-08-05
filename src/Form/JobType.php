<?php

namespace App\Form;

use App\Entity\Job;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Full Time' => 'full-time',
                    'Part Time' => 'part-time',
                    'Freelance' => 'freelance',
                ],
                'placeholder' => 'jobeet.category'
            ])
            ->add('company')
            ->add('logoFile', VichImageType::class, [
                'required' => false,
            ])
            ->add('url', UrlType::class)
            ->add('position')
            ->add('location')
            ->add('description', CKEditorType::class)
            ->add('howToApply', CKEditorType::class)
            ->add('email', EmailType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
