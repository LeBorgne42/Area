<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'text',
                null,
                array(
                    'label' => 'form.text',
                    'attr'  => array(
                        'placeholder' => 'form.text',
                        'class' => ''
                    ),
                    'mapped' => false,
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'form.email',
                    'attr'  => array(
                        'placeholder' => 'form.email',
                        'class' => ''
                    ),
                    'mapped' => false,
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.contact'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => null,
                'translation_domain' => 'front_contact',
            )
        );
    }
}