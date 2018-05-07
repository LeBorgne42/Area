<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
                        'autocomplete' => 'off',
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
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'recycleur',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'autocomplete' => 'off',
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
                        'autocomplete' => 'off',
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
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'barge',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'cargoI',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'cargoV',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'cargoX',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'hunterHeavy',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'corvet',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'corvetLaser',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'fregatePlasma',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'croiser',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'ironClad',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'destroyer',
                null,
                array(
                    'label' => 'form.nbr',
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'autocomplete' => 'off',
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