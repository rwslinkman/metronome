<?php
namespace Metronome\Tests\Util;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SecondForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("field_a", TextType::class, [
                "label" => "Second form"
            ])
            ->add("field_b", TextType::class, [
                "label" => "Second form"
            ])
            ->add("field_c", TextType::class, [
                "label" => "Second form"
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Submit",
                'attr'      => array(
                    'class' => 'btn btn-primary btn-block btn-flat'
                )
            ]);
    }
}