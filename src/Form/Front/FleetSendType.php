<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FleetSendType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'galaxy',
                null,
                array(
                    'label' => 'form.num',
                    'data' => 1,
                    'attr'  => array(
                        'placeholder' => 'form.num',
                        'class' => 'form-control',
                        'min' => '1',
                        'max' => '20',
                    ),
                    'required' => true,
                )
            )
            ->add(
                'sector',
                null,
                array(
                    'label' => 'form.num',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.num',
                        'class' => 'form-control',
                        'min' => '1',
                        'max' => '100',
                        'autocomplete' => 'off',
                    ),
                    'required' => true,
                )
            )
            ->add(
                'planete',
                null,
                array(
                    'label' => 'form.num',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.num',
                        'class' => 'form-control',
                        'min' => '1',
                        'max' => '25',
                        'autocomplete' => 'off',
                    ),
                    'required' => true,
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.sendFleet'));
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