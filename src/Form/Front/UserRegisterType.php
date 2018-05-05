<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserRegisterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                null,
                array(
                    'label' => 'form.username',
                    'attr'  => array(
                        'placeholder' => 'form.username',
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    )
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
                    )
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'form.email',
                    'attr'  => array(
                        'placeholder' => 'form.email',
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    )
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.register'));
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