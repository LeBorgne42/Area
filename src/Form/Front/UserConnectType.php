<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserConnectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'pseudoEmail',
                null,
                array(
                    'label' => 'form.pseudoEmail',
                    'attr'  => array(
                        'placeholder' => 'form.pseudoEmail',
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    ),
                    'mapped' => false,
                )
            )
            ->add(
                'password',
                null,
                array(
                    'label' => 'form.password',
                    'attr'  => array(
                        'placeholder' => 'form.password',
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    ),
                    'mapped' => false,
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.connect'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => 'App\Entity\User',
                'translation_domain' => 'front_index',
            )
        );
    }
}