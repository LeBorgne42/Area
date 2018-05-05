<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserRecoveryType extends AbstractType
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
                        'minlength' => '4',
                        'autocomplete' => 'off',
                    ),
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.getPassword'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => null,
                'translation_domain' => 'front_recoveryPw',
            )
        );
    }
}