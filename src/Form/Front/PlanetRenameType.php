<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\CallbackTransformer;

class PlanetRenameType extends AbstractType
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
            ->add('sendForm', SubmitType::class, array('label' => 'form.renamePlanet'));

        $builder->get('name')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagAsUpper) {
                    return strtolower($tagAsUpper);
                },
                function ($tagAsUpper) {
                    return strtoupper($tagAsUpper);
                }
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         =>  'App\Entity\Planet',
                'translation_domain' => 'front_overview',
            )
        );
    }
}