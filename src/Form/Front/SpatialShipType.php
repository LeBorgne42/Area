<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SpatialShipType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'sonde',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'max' => '50000',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'colonizer',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'max' => '5',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'hunter',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'max' => '50000',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'fregate',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'max' => '50000',
                    ),
                    'required' => false,
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.construct'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => null,
                'translation_domain' => 'front_spatial',
            )
        );
    }
}