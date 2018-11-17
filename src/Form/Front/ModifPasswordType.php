<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ModifPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'oldPassword',
                null,
                [
                    'label' => 'form.oldPassword',
                    'attr'  => [
                        'placeholder' => 'form.oldPassword',
                        'class' => 'game-input',
                        'autocomplete' => 'off',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'password',
                null,
                [
                    'label' => 'form.password',
                    'attr'  => [
                        'placeholder' => 'form.password',
                        'class' => 'game-input',
                        'autocomplete' => 'off',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'confirmPassword',
                null,
                [
                    'label' => 'form.confirmPassword',
                    'attr'  => [
                        'placeholder' => 'form.confirmPassword',
                        'class' => 'game-input',
                        'autocomplete' => 'off',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'planetOrder',
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                [
                    'choices' => $this->getChoices(),
                    'label' => 'form.planetOrder',
                    'attr'  => [
                        'placeholder' => 'form.planetOrder',
                        'class' => 'planetOrder select2',
                    ],
                    'required' => true
                ]
            )
            ->add('sendForm', SubmitType::class, ['label' => 'form.newOptions', 'attr' => ['class' => 'confirm-button float-right']]);
    }

    protected function getChoices()
    {
        return [
            '' => null,
            'Alphabétique' => 'alpha',
            'Position' => 'pos',
            'Colonisation' => 'colo'
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
                'translation_domain' => 'front_options',
            ]
        );
    }
}