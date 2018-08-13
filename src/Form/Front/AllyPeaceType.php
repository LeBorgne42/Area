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
            '10%' => '10',
            '11%' => '11',
            '12%' => '12',
            '13%' => '13',
            '14%' => '14',
            '15%' => '15',
            '16%' => '16',
            '17%' => '17',
            '18%' => '18',
            '19%' => '19',
            '20%' => '20',
            '21%' => '21',
            '22%' => '22',
            '23%' => '23',
            '24%' => '24',
            '25%' => '25',
        );
    }

    protected function getPdgNbr()
    {
        return array(
            'aucun' => '0',
            '25%' => '25',
            '26%' => '26',
            '27%' => '27',
            '28%' => '28',
            '29%' => '29',
            '30%' => '30',
            '31%' => '31',
            '32%' => '32',
            '33%' => '33',
            '34%' => '34',
            '35%' => '35',
            '36%' => '36',
            '37%' => '37',
            '38%' => '38',
            '39%' => '39',
            '40%' => '40',
            '41%' => '41',
            '42%' => '42',
            '43%' => '43',
            '44%' => '44',
            '45%' => '45',
            '46%' => '46',
            '47%' => '47',
            '48%' => '48',
            '49%' => '49',
            '50%' => '50',
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