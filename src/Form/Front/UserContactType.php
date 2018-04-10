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
                'Symfony\Component\Form\Extension\Core\Type\TextareaType',
                array(
                    'label' => 'form.text',
                    'attr'  => array(
                        'placeholder' => 'form.text',
                        'class' => 'form-control',
                        'rows' => 10,
                        'cols' => 75,
                        'maxlength' => '300',
                        'minlength' => '15',
                    ),
                    'required' => true
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
                        'minlength' => '5',
                    ),
                    'required' => true
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