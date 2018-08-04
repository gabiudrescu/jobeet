<?php
/**
 * Created by PhpStorm.
 * User: gabiudrescu
 * Date: 04.08.2018
 * Time: 16:58
 */

namespace App\Form;


use App\Form\DataTransformer\TokenToJobTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class PublishType extends AbstractType
{
    /**
     * @var TokenToJobTransformer $transformer
     */
    private $transformer;

    /**
     * PublishType constructor.
     *
     * @param TokenToJobTransformer $transformer
     */
    public function __construct(TokenToJobTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('job', HiddenType::class);

        $builder->get('job')
            ->addModelTransformer($this->transformer);
    }
}
