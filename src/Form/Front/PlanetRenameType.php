<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
                'id',
                HiddenType::class,
                array(
                    'label' => 'form.id',
                    'attr'  => array(
                        'placeholder' => 'form.id',
                        'class' => 'form-control',
                    ),
                    'required' => false,
                )
            )
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
                        'autocomplete' => 'off',
                    ),
                    'required' => true,
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.renamePlanet'));

        $builder->get('name')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagAsFirstUpper) {
                    return ucfirst($tagAsFirstUpper);
                },
                function ($tagAsFirstUpper) {
                    return ucfirst($tagAsFirstUpper);
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
                'data_class'         =>  null,
                'translation_domain' => 'front_overview',
            )
        );
    }
}