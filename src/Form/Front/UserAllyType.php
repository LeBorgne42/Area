<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserAllyType extends AbstractType
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
                        'autocomplete' => 'off',
                    ),
                    'required' => true
                )
            )
            ->add(
                'sigle',
                null,
                array(
                    'label' => 'form.sigle',
                    'attr'  => array(
                        'placeholder' => 'form.sigle',
                        'class' => 'form-control',
                        'maxlength' => '4',
                        'minlength' => '2',
                        'autocomplete' => 'off',
                    ),
                    'required' => true
                )
            )
            ->add(
                'slogan',
                null,
                array(
                    'label' => 'form.slogan',
                    'attr'  => array(
                        'placeholder' => 'form.slogan',
                        'class' => 'form-control',
                        'maxlength' => '30',
                        'minlength' => '3',
                        'autocomplete' => 'off',
                    ),
                    'required' => true
                )
            )
            ->add(
                'taxe',
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                array(
                    'choices' => $this->getPercentTaxe(),
                    'label' => 'form.taxeAlly',
                    'attr'  => array(
                        'placeholder' => 'form.taxe',
                        'class' => 'form-control select2',
                    ),
                    'required' => true
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.send'));

        $builder->get('sigle')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagAsUpper) {
                    return strtolower($tagAsUpper);
                },
                function ($tagAsUpper) {
                    return strtoupper($tagAsUpper);
                }
            ));
        $builder->get('name')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagAsFirstUpper) {
                    return ucfirst($tagAsFirstUpper);
                },
                function ($tagAsFirstUpper) {
                    return ucfirst($tagAsFirstUpper);
                }
            ));
        $builder->get('slogan')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagAsFirstUpper) {
                    return ucfirst($tagAsFirstUpper);
                },
                function ($tagAsFirstUpper) {
                    return ucfirst($tagAsFirstUpper);
                }
            ));
    }

    protected function getPercentTaxe()
    {
        return array(
            '01%' => '1',
            '02%' => '2',
            '03%' => '3',
            '04%' => '4',
            '05%' => '5',
            '06%' => '6',
            '07%' => '7',
            '08%' => '8',
            '09%' => '9',
            '10%' => '10'
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => 'App\Entity\Ally',
                'translation_domain' => 'front_ally',
            )
        );
    }
}