<?php
/**
 * Created by PhpStorm.
 * User: gabiudrescu
 * Date: 05.08.2018
 * Time: 12:42
 */

namespace App\Form;


use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class JobEditType extends JobType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('public');
        $builder->add('extend', CheckboxType::class, [
            'mapped' => false,
            'required' => false,
        ]);
    }
}
