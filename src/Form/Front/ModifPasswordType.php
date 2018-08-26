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
                array(
                    'label' => 'form.oldPassword',
                    'attr'  => array(
                        'placeholder' => 'form.oldPassword',
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'password',
                null,
                array(
                    'label' => 'form.password',
                    'attr'  => array(
                        'placeholder' => 'form.password',
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'confirmPassword',
                null,
                array(
                    'label' => 'form.confirmPassword',
                    'attr'  => array(
                        'placeholder' => 'form.confirmPassword',
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    ),
                    'required' => false,
                )
            )
            ->add(
                'planetOrder',
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                array(
                    'choices' => $this->getChoices(),
                    'label' => 'form.planetOrder',
                    'attr'  => array(
                        'placeholder' => 'form.planetOrder',
                        'class' => 'form-control select2',
                    ),
                    'required' => true
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.newOptions'));
    }

    protected function getChoices()
    {
        return array(
            '' => null,
            'Alphabétique' => 'alpha',
            'Position' => 'pos',
            'Colonisation' => 'colo'
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
                'translation_domain' => 'front_options',
            )
        );
    }
}