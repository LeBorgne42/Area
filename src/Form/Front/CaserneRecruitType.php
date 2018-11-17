<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CaserneRecruitType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'soldier',
                null,
                [
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => [
                        'placeholder' => 'form.nbr',
                        'class' => 'game-input text-right',
                        'min' => '0',
                        'max' => '50000',
                        'autocomplete' => 'off',
                    ],
                    'required' => true,
                ]
            )
            ->add('sendForm', SubmitType::class, ['label' => 'form.recruitSoldier', 'attr' => ['class' => 'confirm-button float-right']]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'         =>  null,
                'translation_domain' => 'front_soldier',
            ]
        );
    }
}