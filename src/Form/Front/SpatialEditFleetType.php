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
            ->add(
                'moreNiobium',
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
                'lessNiobium',
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
                'moreWater',
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
                'lessWater',
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
                'moreSoldier',
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
                'lessSoldier',
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
                'moreWorker',
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
                'lessWorker',
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
                'moreScientist',
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
                'lessScientist',
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
                'data_class'         => null,
                'translation_domain' => 'front_fleet',
            )
        );
    }
}