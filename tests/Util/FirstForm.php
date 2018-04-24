<?php
namespace Metronome\Tests\Util;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FirstForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("field_one", TextType::class, [
                "label" => "First form"
            ])
            ->add("field_two", TextType::class, [
                "label" => "First form"
            ])
            ->add("field_three", TextType::class, [
                "label" => "First form"
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Submit",
                'attr'      => array(
                    'class' => 'btn btn-primary btn-block btn-flat'
                )
            ]);
    }
}