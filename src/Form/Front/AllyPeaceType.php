<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AllyPeaceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'type',
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                array(
                    'choices' => $this->getType(),
                    'label' => 'form.type',
                    'attr'  => array(
                        'placeholder' => 'form.type',
                        'class' => 'form-control select2',
                    ),
                    'required' => true
                )
            )
            ->add(
                'planetNbr',
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                array(
                    'choices' => $this->getPlanetNbr(),
                    'label' => 'form.type',
                    'attr'  => array(
                        'placeholder' => 'form.type',
                        'class' => 'form-control select2',
                    ),
                    'required' => true
                )
            )
            ->add(
                'taxeNbr',
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                array(
                    'choices' => $this->getTaxeNbr(),
                    'label' => 'form.type',
                    'attr'  => array(
                        'placeholder' => 'form.type',
                        'class' => 'form-control select2',
                    ),
                    'required' => true
                )
            )
            ->add(
                'pdgNbr',
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                array(
                    'choices' => $this->getPdgNbr(),
                    'label' => 'form.type',
                    'attr'  => array(
                        'placeholder' => 'form.type',
                        'class' => 'form-control select2',
                    ),
                    'required' => true
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.add'));
    }

    protected function getType()
    {
        return array(
            'Proposer' => '0',
            'Réclamer' => '1',
        );
    }

    protected function getPlanetNbr()
    {
        return array(
            'aucune' => '0',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
        );
    }

    protected function getTaxeNbr()
    {
        return array(
            'aucun' => '0',
            '10%' => '1',
            '11%' => '2',
            '12%' => '3',
            '13%' => '4',
            '14%' => '5',
            '15%' => '6',
            '16%' => '7',
            '17%' => '8',
            '18%' => '9',
            '19%' => '10',
            '20%' => '11',
        );
    }

    protected function getPdgNbr()
    {
        return array(
            'aucun' => '0',
            '10%' => '1',
            '11%' => '2',
            '12%' => '3',
            '13%' => '4',
            '14%' => '5',
            '15%' => '6',
            '16%' => '7',
            '17%' => '8',
            '18%' => '9',
            '19%' => '10',
            '20%' => '11',
            '21%' => '12',
            '22%' => '13',
            '23%' => '14',
            '24%' => '15',
            '25%' => '16',
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