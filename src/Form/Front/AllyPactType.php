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
                [
                    'label' => 'form.allyName',
                    'attr'  => [
                        'placeholder' => 'form.allyName',
                        'class' => 'game-input',
                        'minlength' => '1',
                        'autocomplete' => 'off',
                    ],
                    'required' => true
                ]
            )
            ->add(
                'pactType',
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                [
                    'choices' => $this->getPactType(),
                    'label' => 'form.pactType',
                    'attr'  => [
                        'placeholder' => 'form.pactType',
                        'class' => 'select2 game-input',
                    ],
                    'required' => true
                ]
            )
            ->add('sendForm', SubmitType::class, ['label' => 'form.add', 'attr' => ['class' => 'confirm-button']]);
    }

    protected function getPactType()
    {
        return [
            'Pacte défensif' => '1',
            'Pacte de non-agression' => '2',
            'Déclarer guerre' => '3',
        ];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'         => null,
                'translation_domain' => 'front_ally',
            ]
        );
    }
}