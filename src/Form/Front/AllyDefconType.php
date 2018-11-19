<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AllyDefconType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'defcon',
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                [
                    'choices' => $this->getDefcon(),
                    'label' => 'form.defcon',
                    'attr'  => [
                        'placeholder' => 'form.defcon',
                        'class' => 'game-input select2',
                        'autocomplete' => 'off',
                        'style' => 'width:150px;'
                    ],
                    'required' => true
                ]
            )
            ->add('sendForm', SubmitType::class, ['label' => 'form.send', 'attr' => ['class' => 'confirm-button']]);
    }

    protected function getDefcon()
    {
        return [
            '0 - Paix' => '0',
            '1 - Préparation militaire' => '1',
            '2 - Guerre imminente' => '2',
            '3 - En guerre' => '3'
        ];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'         => 'App\Entity\Ally',
                'translation_domain' => 'front_defcon',
            ]
        );
    }
}