<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AllyPactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'allyName',
                null,
                array(
                    'label' => 'form.allyName',
                    'attr'  => array(
                        'placeholder' => 'form.allyName',
                        'class' => 'form-control',
                        'minlength' => '1',
                        'autocomplete' => 'off',
                    ),
                    'required' => true
                )
            )
            ->add(
                'pactType',
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                array(
                    'choices' => $this->getPactType(),
                    'label' => 'form.pactType',
                    'attr'  => array(
                        'placeholder' => 'form.pactType',
                        'class' => 'form-control select2',
                    ),
                    'required' => true
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.add'));
    }

    protected function getPactType()
    {
        return array(
            'Proposer alliance' => '1',
            'Proposer un pna' => '2',
            'Déclarer guerre' => '3',
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => null,
                'translation_domain' => 'front_ally',
            )
        );
    }
}