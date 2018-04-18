<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AllyGradeType extends AbstractType
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
                        'minlength' => '3',
                    ),
                    'required' => true,
                    'mapped' => true,
                )
            )
            ->add(
                'canRecruit',
                CheckboxType::class,
                array(
                    'label' => 'form.name',
                    'attr'  => array(
                        'placeholder' => 'form.name',
                        'class' => '',
                    ),
                    'required' => false,
                    'mapped' => true,
                )
            )
            ->add(
                'canKick',
                CheckboxType::class,
                array(
                    'label' => 'form.name',
                    'attr'  => array(
                        'placeholder' => 'form.name',
                        'class' => '',
                    ),
                    'required' => false
                )
            )
            ->add(
                'canWar',
                CheckboxType::class,
                array(
                    'label' => 'form.name',
                    'attr'  => array(
                        'placeholder' => 'form.name',
                        'class' => '',
                    ),
                    'required' => false
                )
            )
            ->add(
                'canPeace',
                CheckboxType::class,
                array(
                    'label' => 'form.name',
                    'attr'  => array(
                        'placeholder' => 'form.name',
                        'class' => '',
                    ),
                    'required' => false
                )
            )
            ->add(
                'placement',
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                array(
                    'choices' => $this->getPlacement(),
                    'label' => 'form.placement',
                    'attr'  => array(
                        'placeholder' => 'form.placement',
                        'class' => 'form-control select2',
                    ),
                    'required' => true
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.send'));

        $builder->get('name')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagAsFirstUpper) {
                    return lcfirst($tagAsFirstUpper);
                },
                function ($tagAsFirstUpper) {
                    return ucfirst($tagAsFirstUpper);
                }
            ));
    }

    protected function getPlacement()
    {
        return array(
            'Dirigeant' => '1',
            'Bras droit' => '2',
            'Officier' => '3',
            'Normal' => '4'
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => 'App\Entity\Grade',
                'translation_domain' => 'front_grade',
            )
        );
    }
}