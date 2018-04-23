<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SpatialEditFleetType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                null,
                array(
                    'label' => 'form.name',
                    'attr'  => array(
                        'placeholder' => 'form.name',
                        'class' => 'form-control',
                        'maxlength' => '15',
                        'minlength' => '2',
                    ),
                    'required' => true,
                    'mapped' => true,
                )
            )
            ->add(
                'moreSonde',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                    ),
                    'required' => false,
                    'mapped' => false,
                )
            )
            ->add(
                'lessSonde',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                    ),
                    'required' => false,
                    'mapped' => false,
                )
            )
            ->add(
                'moreColonizer',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                    ),
                    'required' => false,
                    'mapped' => false,
                )
            )
            ->add(
                'lessColonizer',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                    ),
                    'required' => false,
                    'mapped' => false,
                )
            )
            ->add(
                'moreRecycleur',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                    ),
                    'required' => false,
                    'mapped' => false,
                )
            )
            ->add(
                'lessRecycleur',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                    ),
                    'required' => false,
                    'mapped' => false,
                )
            )
            ->add(
                'moreHunter',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                    ),
                    'required' => false,
                    'mapped' => false,
                )
            )
            ->add(
                'lessHunter',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                    ),
                    'required' => false,
                    'mapped' => false,
                )
            )
            ->add(
                'moreFregate',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                    ),
                    'required' => false,
                    'mapped' => false,
                )
            )
            ->add(
                'lessFregate',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                    ),
                    'required' => false,
                    'mapped' => false,
                )
            )
            ->add(
                'moreBarge',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                    ),
                    'required' => false,
                    'mapped' => false,
                )
            )
            ->add(
                'lessBarge',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                    ),
                    'required' => false,
                    'mapped' => false,
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.manageFleet'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => 'App\Entity\Fleet',
                'translation_domain' => 'front_fleet',
            )
        );
    }
}