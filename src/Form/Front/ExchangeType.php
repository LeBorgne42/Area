<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;

class ExchangeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'exchangeType',
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                array(
                    'choices' => $this->getTrade(),
                    'label' => 'form.pactType',
                    'attr'  => array(
                        'placeholder' => 'form.exchangeType',
                        'class' => 'form-control select2',
                    ),
                    'required' => true
                )
            )
            ->add(
                'amount',
                null,
                array(
                    'label' => 'form.num',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.num',
                        'class' => 'form-control text-right',
                        'min' => '0',
                        'style' => 'height: 25px',
                        'autocomplete' => 'off',
                    ),
                    'required' => true,
                    'mapped' => true,
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.transfert'));
    }

    protected function getTrade()
    {
        return array(
            'Donner' => '1',
            'Retirer' => '2',
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