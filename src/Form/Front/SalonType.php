<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SalonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'content',
                null,
                array(
                    'label' => 'form.content',
                    'attr'  => array(
                        'placeholder' => 'form.content',
                        'class' => 'form-control',
                        'maxlength' => '200',
                        'minlength' => '1',
                    ),
                    'required' => true,
                    'mapped' => true,
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.sendMessage'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => null,
                'translation_domain' => 'front_salon',
            )
        );
    }
}